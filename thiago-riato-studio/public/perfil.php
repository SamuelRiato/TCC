<?php
require __DIR__ . '/../src/bootstrap.php';
$u = require_login();

$erros = [];
$v = [
    'nome'       => $u['nome'] ?? '',
    'email'      => $u['email'] ?? '',
    'telefone'   => $u['telefone'] ?? '',
    'nascimento' => $u['nascimento'] ?? '',
    'peso'       => $u['peso'] ?? '',
    'altura'     => $u['altura'] ?? '',
    'objetivo'   => $u['objetivo'] ?? OBJETIVOS[0],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($v) as $k) {
        $v[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    $v['email'] = mb_strtolower($v['email']);
    $v['peso']  = str_replace(',', '.', $v['peso']);
    $novaFoto   = null;

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $erros['geral'] = 'Sua sessão expirou. Recarregue a página e tente de novo.';
    } else {
        if (mb_strlen($v['nome']) < 3) {
            $erros['nome'] = 'Informe seu nome completo.';
        }
        if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Informe um e-mail válido.';
        } else {
            $outro = user_find_by_email($v['email']);
            if ($outro && $outro['id'] !== $u['id']) {
                $erros['email'] = 'Este e-mail já está em uso.';
            }
        }
        $digitos = preg_replace('/\D/', '', $v['telefone']);
        if ($v['telefone'] !== '' && (strlen($digitos) < 10 || strlen($digitos) > 11)) {
            $erros['telefone'] = 'Informe DDD e número.';
        }
        if ($v['nascimento'] !== '') {
            $d = DateTimeImmutable::createFromFormat('Y-m-d', $v['nascimento']);
            if (!$d || $d->format('Y-m-d') !== $v['nascimento'] || $v['nascimento'] > date('Y-m-d')) {
                $erros['nascimento'] = 'Informe uma data válida.';
            }
        }
        if ($v['peso'] !== '' && (!is_numeric($v['peso']) || $v['peso'] < 20 || $v['peso'] > 400)) {
            $erros['peso'] = 'Informe um peso entre 20 e 400 kg.';
        }
        if ($v['altura'] !== '' && (!ctype_digit($v['altura']) || $v['altura'] < 80 || $v['altura'] > 260)) {
            $erros['altura'] = 'Informe uma altura entre 80 e 260 cm.';
        }
        if (!in_array($v['objetivo'], OBJETIVOS, true)) {
            $erros['objetivo'] = 'Escolha uma das opções.';
        }

        // Foto de perfil (opcional)
        $f = $_FILES['avatar'] ?? null;
        if ($f && $f['error'] !== UPLOAD_ERR_NO_FILE) {
            $tipos = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
            if ($f['error'] !== UPLOAD_ERR_OK) {
                $erros['avatar'] = 'Não foi possível enviar a foto. Tente de novo.';
            } elseif ($f['size'] > 2 * 1024 * 1024) {
                $erros['avatar'] = 'A foto deve ter no máximo 2 MB.';
            } else {
                $info = @getimagesize($f['tmp_name']);
                if (!$info || !isset($tipos[$info[2]])) {
                    $erros['avatar'] = 'Use uma imagem JPG, PNG ou WEBP.';
                } else {
                    $novaFoto = ['tmp' => $f['tmp_name'], 'ext' => $tipos[$info[2]]];
                }
            }
        }

        if (!$erros) {
            $campos = [
                'nome'       => $v['nome'],
                'email'      => $v['email'],
                'telefone'   => $v['telefone'],
                'nascimento' => $v['nascimento'],
                'peso'       => $v['peso'],
                'altura'     => $v['altura'],
                'objetivo'   => $v['objetivo'],
            ];
            if ($novaFoto) {
                if (!is_dir(AVATAR_DIR)) {
                    mkdir(AVATAR_DIR, 0775, true);
                }
                $arquivo = bin2hex(random_bytes(12)) . '.' . $novaFoto['ext'];
                if (move_uploaded_file($novaFoto['tmp'], AVATAR_DIR . $arquivo)) {
                    if (!empty($u['avatar']) && is_file(AVATAR_DIR . basename($u['avatar']))) {
                        @unlink(AVATAR_DIR . basename($u['avatar']));
                    }
                    $campos['avatar'] = $arquivo;
                }
            }
            user_update($u['id'], $campos);
            flash('Alterações salvas.');
            redirect('perfil.php');
        }
    }
}

// Reflete no cabeçalho/foto os dados atuais (ou os que o usuário acabou de digitar)
$exibir = array_merge($u, ['nome' => $v['nome'] !== '' ? $v['nome'] : $u['nome']]);

app_top('Meu Perfil', 'perfil');
?>
<header class="page-head">
    <div>
        <h1>Meu Perfil</h1>
        <p class="sub">Mantenha seus dados e objetivos atualizados.</p>
    </div>
    <button type="submit" form="form-perfil" class="btn btn--sm"><span>Salvar Alterações</span><?= icon('arrow-right', 16) ?></button>
</header>

<?php if (!empty($erros['geral'])): ?>
    <div class="alerta" role="alert"><?= e($erros['geral']) ?></div>
<?php endif; ?>

<form id="form-perfil" method="post" enctype="multipart/form-data" novalidate class="perfil-grid">
    <?= csrf_field() ?>

    <section class="card perfil-avatar" aria-label="Foto de perfil">
        <div class="avatar-troca">
            <span data-avatar-atual><?= avatar_html($exibir, 'avatar avatar--xl') ?></span>
            <img class="avatar avatar--xl" alt="" data-avatar-previa hidden>
            <label class="avatar-troca__botao" for="avatar" title="Trocar foto">
                <?= icon('camera', 16) ?><span class="sr-only">Trocar foto de perfil</span>
            </label>
            <input class="sr-only" type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" data-avatar-input>
        </div>
        <strong><?= e($exibir['nome']) ?></strong>
        <small><?= e($u['email']) ?></small>
        <?php if (!empty($erros['avatar'])): ?>
            <p class="campo__erro" role="alert" data-avatar-erro><?= e($erros['avatar']) ?></p>
        <?php else: ?>
            <p class="campo__erro" role="alert" data-avatar-erro hidden></p>
        <?php endif; ?>
    </section>

    <section class="card perfil-dados">
        <fieldset>
            <legend>Informações pessoais</legend>
            <div class="grid-2">
                <?php
                campo(['id' => 'nome', 'nome' => 'nome', 'rotulo' => 'Nome completo', 'icone' => 'user',
                       'valor' => $v['nome'], 'autocomplete' => 'name', 'erro' => $erros['nome'] ?? null,
                       'attrs' => ['required' => '']]);
                campo(['id' => 'email', 'nome' => 'email', 'rotulo' => 'E-mail', 'tipo' => 'email', 'icone' => 'mail',
                       'valor' => $v['email'], 'autocomplete' => 'email', 'erro' => $erros['email'] ?? null,
                       'attrs' => ['required' => '']]);
                campo(['id' => 'telefone', 'nome' => 'telefone', 'rotulo' => 'Telefone', 'tipo' => 'tel', 'icone' => 'phone',
                       'valor' => $v['telefone'], 'placeholder' => '(11) 99999-9999', 'autocomplete' => 'tel',
                       'erro' => $erros['telefone'] ?? null, 'attrs' => ['data-mask' => 'telefone', 'inputmode' => 'tel']]);
                campo(['id' => 'nascimento', 'nome' => 'nascimento', 'rotulo' => 'Data de Nascimento', 'tipo' => 'date', 'icone' => 'calendar',
                       'valor' => $v['nascimento'], 'autocomplete' => 'bday', 'erro' => $erros['nascimento'] ?? null,
                       'attrs' => ['max' => date('Y-m-d')]]);
                ?>
            </div>
        </fieldset>

        <fieldset>
            <legend>Informações Físicas</legend>
            <div class="grid-2">
                <?php
                campo(['id' => 'peso', 'nome' => 'peso', 'rotulo' => 'Peso (kg)', 'tipo' => 'number', 'icone' => 'weight',
                       'valor' => (string) $v['peso'], 'sufixo' => 'kg', 'erro' => $erros['peso'] ?? null,
                       'attrs' => ['step' => '0.1', 'min' => 20, 'max' => 400, 'inputmode' => 'decimal']]);
                campo(['id' => 'altura', 'nome' => 'altura', 'rotulo' => 'Altura (cm)', 'tipo' => 'number', 'icone' => 'ruler',
                       'valor' => (string) $v['altura'], 'sufixo' => 'cm', 'erro' => $erros['altura'] ?? null,
                       'attrs' => ['step' => '1', 'min' => 80, 'max' => 260, 'inputmode' => 'numeric']]);
                ?>
            </div>
            <div class="campo<?= !empty($erros['objetivo']) ? ' campo--erro' : '' ?>">
                <label for="objetivo">Objetivo principal</label>
                <div class="campo__wrap tem-select">
                    <?= icon('target') ?>
                    <select id="objetivo" name="objetivo">
                        <?php foreach (OBJETIVOS as $o): ?>
                            <option value="<?= e($o) ?>" <?= $v['objetivo'] === $o ? 'selected' : '' ?>><?= e($o) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (!empty($erros['objetivo'])): ?><p class="campo__erro"><?= e($erros['objetivo']) ?></p><?php endif; ?>
            </div>
        </fieldset>
    </section>
</form>
<?php app_bottom(); ?>
