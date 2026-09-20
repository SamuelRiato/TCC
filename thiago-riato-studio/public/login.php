<?php
require __DIR__ . '/../src/bootstrap.php';
guest_only();

$erros = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $erros['geral'] = 'Sua sessão expirou. Recarregue a página e tente de novo.';
    } elseif (($_SESSION['bloqueio_ate'] ?? 0) > time()) {
        $erros['geral'] = 'Muitas tentativas. Aguarde um minuto e tente de novo.';
    } elseif ($email === '' || $senha === '') {
        if ($email === '') { $erros['email'] = 'Informe seu e-mail.'; }
        if ($senha === '') { $erros['senha'] = 'Informe sua senha.'; }
    } else {
        $u = user_find_by_email($email);
        if ($u && password_verify($senha, $u['senha_hash'])) {
            unset($_SESSION['tentativas'], $_SESSION['bloqueio_ate']);
            login_user($u);
            redirect('inicio.php');
        }
        usleep(400000);
        $_SESSION['tentativas'] = ($_SESSION['tentativas'] ?? 0) + 1;
        if ($_SESSION['tentativas'] >= 5) {
            $_SESSION['bloqueio_ate'] = time() + 60;
            $_SESSION['tentativas'] = 0;
        }
        $erros['geral'] = 'E-mail ou senha incorretos.';
    }
}

auth_top('Entrar');
?>
<div class="auth-card">
    <h2>Login</h2>
    <p class="auth-card__sub">Seja bem-vindo de volta! Faça seu login para treinar.</p>

    <?php if (!empty($erros['geral'])): ?>
        <div class="alerta" role="alert"><?= e($erros['geral']) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <?= csrf_field() ?>
        <?php
        campo(['id' => 'email', 'nome' => 'email', 'rotulo' => 'E-mail', 'tipo' => 'email', 'icone' => 'mail',
               'valor' => $email, 'placeholder' => 'seu-email@dominio.com', 'autocomplete' => 'email',
               'erro' => $erros['email'] ?? null, 'attrs' => ['required' => '']]);
        campo(['id' => 'senha', 'nome' => 'senha', 'rotulo' => 'Senha', 'tipo' => 'password', 'icone' => 'lock',
               'placeholder' => '••••••••', 'autocomplete' => 'current-password',
               'erro' => $erros['senha'] ?? null, 'attrs' => ['required' => '']]);
        ?>
        <div class="esqueci">
            <button type="button" class="link" data-esqueci aria-controls="aviso-esqueci">Esqueceu sua senha?</button>
        </div>
        <p class="aviso" id="aviso-esqueci" hidden>A recuperação por e-mail ainda não está ativa. Fale com o Thiago para redefinir sua senha.</p>

        <button class="btn" type="submit"><span>Entrar</span><?= icon('arrow-right') ?></button>
    </form>

    <p class="auth-card__rodape">Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
</div>
<?php auth_bottom(); ?>
