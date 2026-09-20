<?php
declare(strict_types=1);

function head(string $titulo, string $classeBody): void
{
    ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2B322D">
    <meta name="csrf" content="<?= e(csrf_token()) ?>">
    <title><?= e($titulo) ?> | <?= e(APP_NOME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/app.js" defer></script>
</head>
<body class="<?= e($classeBody) ?>">
<?php
}

/* ---------- Telas de login e cadastro (painel escuro + cartão) ---------- */

function auth_top(string $titulo): void
{
    head($titulo, 'pagina-auth');
    ?>
<main class="auth">
    <aside class="auth__marca">
        <div class="auth__logo"><?= logo() ?></div>
        <div class="auth__mensagem">
            <h1>Treino com propósito. Evolução de verdade.</h1>
            <p>Acompanhe seus treinos, sua rotina e cada conquista em um só lugar.</p>
        </div>
        <small class="auth__copy">© <?= date('Y') ?> <?= e(APP_NOME) ?></small>
    </aside>
    <section class="auth__painel">
<?php
}

function auth_bottom(): void
{
    ?>
    </section>
</main>
</body>
</html>
<?php
}

/* ---------- Área logada (sidebar + conteúdo) ---------- */

function form_sair(string $classe): void
{
    ?>
    <form method="post" action="logout.php" class="<?= e($classe) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="icon-btn" aria-label="Sair da conta" title="Sair"><?= icon('log-out') ?></button>
    </form>
    <?php
}

function app_top(string $titulo, string $ativo): void
{
    $u = current_user() ?? ['nome' => 'Aluno'];
    $nav = [
        'inicio'  => ['inicio.php',  'Início',  'home'],
        'treinos' => ['treinos.php', 'Treinos', 'dumbbell'],
        'agenda'  => ['agenda.php',  'Agenda',  'calendar'],
        'perfil'  => ['perfil.php',  'Perfil',  'user'],
    ];
    head($titulo, 'pagina-app');
    ?>
<div class="app">
    <header class="topbar">
        <a href="inicio.php" aria-label="Ir para o início"><?= logo() ?></a>
        <?php form_sair('topbar__sair'); ?>
    </header>

    <aside class="sidebar">
        <a class="sidebar__logo" href="inicio.php" aria-label="Ir para o início"><?= logo() ?></a>
        <nav class="nav" aria-label="Principal">
            <ul>
                <?php foreach ($nav as $chave => [$href, $rotulo, $icone]): ?>
                    <li>
                        <a class="nav__item<?= $chave === $ativo ? ' is-ativo' : '' ?>" href="<?= e($href) ?>"
                           <?= $chave === $ativo ? 'aria-current="page"' : '' ?>>
                            <?= icon($icone) ?><span><?= e($rotulo) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="sidebar__usuario">
            <?= avatar_html($u, 'avatar avatar--sm') ?>
            <div class="sidebar__nome">
                <strong><?= e($u['nome']) ?></strong>
                <small>Aluno</small>
            </div>
            <?php form_sair('sidebar__sair'); ?>
        </div>
    </aside>

    <main class="main">
<?php
}

function app_bottom(): void
{
    $msg = pull_flash();
    ?>
    </main>
</div>
<?php if ($msg): ?>
    <div class="toast" role="status" data-toast><?= icon('check') ?><span><?= e($msg) ?></span></div>
<?php endif; ?>
</body>
</html>
<?php
}
