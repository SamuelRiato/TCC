<?php
require __DIR__ . '/../src/bootstrap.php';
guest_only();

$erros = [];
$v = ['nome' => '', 'telefone' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nome']     = trim((string) ($_POST['nome'] ?? ''));
    $v['telefone'] = trim((string) ($_POST['telefone'] ?? ''));
    $v['email']    = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
    $senha         = (string) ($_POST['senha'] ?? '');
    $confirmar     = (string) ($_POST['confirmar'] ?? '');

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $erros['geral'] = 'Sua sessão expirou. Recarregue a página e tente de novo.';
    } else {
        if (mb_strlen($v['nome']) < 3) {
            $erros['nome'] = 'Informe seu nome completo.';
        }
        $digitos = preg_replace('/\D/', '', $v['telefone']);
        if (strlen($digitos) < 10 || strlen($digitos) > 11) {
            $erros['telefone'] = 'Informe DDD e número.';
        }
        if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Informe um e-mail válido.';
        }
        if (strlen($senha) < 8) {
            $erros['senha'] = 'Use pelo menos 8 caracteres.';
        }
        if ($confirmar !== $senha) {
            $erros['confirmar'] = 'As senhas não são iguais.';
        }

        if (!$erros) {
            $novo = user_create([
                'nome'       => $v['nome'],
                'telefone'   => $v['telefone'],
                'email'      => $v['email'],
                'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
            ]);
            if ($novo === null) {
                $erros['email'] = 'Já existe uma conta com este e-mail.';
            } else {
                login_user($novo);
                flash('Conta criada. Bem-vindo ao estúdio!');
                redirect('inicio.php');
            }
        }
    }
}

auth_top('Cadastro');
?>
<div class="auth-card auth-card--larga">
    <h2>Cadastro</h2>
    <p class="auth-card__sub">Crie sua conta para começar sua jornada fitness</p>

    <?php if (!empty($erros['geral'])): ?>
        <div class="alerta" role="alert"><?= e($erros['geral']) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="grid-2">
            <?php
            campo(['id' => 'nome', 'nome' => 'nome', 'rotulo' => 'Nome completo', 'icone' => 'user',
                   'valor' => $v['nome'], 'placeholder' => 'Digite seu nome', 'autocomplete' => 'name',
                   'erro' => $erros['nome'] ?? null, 'attrs' => ['required' => '']]);
            campo(['id' => 'telefone', 'nome' => 'telefone', 'rotulo' => 'Telefone', 'tipo' => 'tel', 'icone' => 'phone',
                   'valor' => $v['telefone'], 'placeholder' => '(11) 99999-9999', 'autocomplete' => 'tel',
                   'erro' => $erros['telefone'] ?? null, 'attrs' => ['required' => '', 'data-mask' => 'telefone', 'inputmode' => 'tel']]);
            ?>
        </div>
        <?php
        campo(['id' => 'email', 'nome' => 'email', 'rotulo' => 'E-mail', 'tipo' => 'email', 'icone' => 'mail',
               'valor' => $v['email'], 'placeholder' => 'seu-email@dominio.com', 'autocomplete' => 'email',
               'erro' => $erros['email'] ?? null, 'attrs' => ['required' => '']]);
        ?>
        <div class="grid-2">
            <?php
            campo(['id' => 'senha', 'nome' => 'senha', 'rotulo' => 'Senha', 'tipo' => 'password', 'icone' => 'lock',
                   'placeholder' => '••••••••', 'autocomplete' => 'new-password',
                   'erro' => $erros['senha'] ?? null, 'attrs' => ['required' => '', 'minlength' => 8]]);
            campo(['id' => 'confirmar', 'nome' => 'confirmar', 'rotulo' => 'Confirmar Senha', 'tipo' => 'password', 'icone' => 'lock',
                   'placeholder' => '••••••••', 'autocomplete' => 'new-password',
                   'erro' => $erros['confirmar'] ?? null, 'attrs' => ['required' => '']]);
            ?>
        </div>

        <button class="btn" type="submit"><span>Criar Conta</span><?= icon('arrow-right') ?></button>
    </form>

    <p class="auth-card__rodape">Já tem uma conta? <a href="login.php">Faça login</a></p>
</div>
<?php auth_bottom(); ?>
