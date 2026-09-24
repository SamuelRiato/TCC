<?php
declare(strict_types=1);

function e(?string $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $para): void
{
    header('Location: ' . $para);
    exit;
}

/* ---------- CSRF e mensagens ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid($token): bool
{
    $esperado = $_SESSION['csrf'] ?? '';
    return is_string($token) && $esperado !== '' && hash_equals($esperado, $token);
}

function flash(string $msg): void
{
    $_SESSION['flash'] = $msg;
}

function pull_flash(): ?string
{
    $m = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_string($m) ? $m : null;
}

/* ---------- Nomes e avatar ---------- */

function partes_nome(string $nome): array
{
    return preg_split('/\s+/', trim($nome), -1, PREG_SPLIT_NO_EMPTY) ?: [];
}

function primeiro_nome(string $nome): string
{
    return partes_nome($nome)[0] ?? 'Aluno';
}

function inicial(string $s): string
{
    return preg_match('/\p{L}/u', $s, $m) ? mb_strtoupper($m[0]) : '';
}

function avatar_html(array $u, string $classe = 'avatar'): string
{
    if (!empty($u['avatar'])) {
        return '<img class="' . e($classe) . '" src="uploads/avatars/' . e($u['avatar']) . '" alt="">';
    }
    $p = partes_nome($u['nome'] ?? '');
    $ini = inicial($p[0] ?? '') . (count($p) > 1 ? inicial(end($p)) : '');
    return '<span class="' . e($classe) . ' avatar--ini" aria-hidden="true">' . e($ini) . '</span>';
}

/* ---------- Ícones e logo ---------- */

function icon(string $nome, int $tamanho = 18): string
{
    static $p = [
        'home'        => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'dumbbell'    => '<path d="M6.5 6.5v11M17.5 6.5v11M3.5 9v6M20.5 9v6M6.5 12h11"/>',
        'calendar'    => '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'user'        => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'bell'        => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
        'check'       => '<path d="M20 6 9 17l-5-5"/>',
        'chevron'     => '<path d="m9 18 6-6-6-6"/>',
        'arrow-right' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'mail'        => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
        'lock'        => '<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'eye'         => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
        'eye-off'     => '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><path d="m2 2 20 20"/>',
        'phone'       => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'camera'      => '<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"/><circle cx="12" cy="13" r="3"/>',
        'ruler'       => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2M11.5 9.5l2-2M8.5 6.5l2-2M17.5 15.5l2-2"/>',
        'weight'      => '<circle cx="12" cy="5" r="3"/><path d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z"/>',
        'target'      => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'heart-pulse' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/>',
        'log-out'     => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
        'clock'       => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
    ];
    return '<svg class="icon" width="' . $tamanho . '" height="' . $tamanho . '" viewBox="0 0 24 24" fill="none" '
         . 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" '
         . 'aria-hidden="true" focusable="false">' . ($p[$nome] ?? '') . '</svg>';
}

function logo(): string
{
    return '<span class="logo" role="img" aria-label="' . e(APP_NOME) . '">'
         . '<span class="logo__marca"><b>T</b><b>R</b></span>'
         . '<span class="logo__texto"><span class="logo__nome"><em>THIAGO</em> RIATO</span><small>PERSONAL STUDIO</small></span>'
         . '</span>';
}

/* ---------- Campo de formulário ---------- */

function campo(array $o): void
{
    $id     = $o['id'];
    $tipo   = $o['tipo'] ?? 'text';
    $erro   = $o['erro'] ?? null;
    $senha  = $tipo === 'password';
    $sufixo = $o['sufixo'] ?? null;
    ?>
    <div class="campo<?= $erro ? ' campo--erro' : '' ?>">
        <label for="<?= e($id) ?>"><?= e($o['rotulo']) ?></label>
        <div class="campo__wrap<?= $senha ? ' tem-toggle' : '' ?><?= $sufixo ? ' tem-sufixo' : '' ?>">
            <?= icon($o['icone']) ?>
            <input id="<?= e($id) ?>" name="<?= e($o['nome']) ?>" type="<?= e($tipo) ?>"
                   value="<?= $senha ? '' : e($o['valor'] ?? '') ?>"
                   placeholder="<?= e($o['placeholder'] ?? '') ?>"
                   autocomplete="<?= e($o['autocomplete'] ?? 'off') ?>"
                   <?php if ($erro): ?>aria-invalid="true" aria-describedby="erro-<?= e($id) ?>"<?php endif; ?>
                   <?php foreach (($o['attrs'] ?? []) as $k => $v) { echo e($k) . '="' . e((string) $v) . '" '; } ?>>
            <?php if ($senha): ?>
                <button type="button" class="campo__toggle" data-toggle-senha aria-pressed="false" aria-label="Mostrar senha">
                    <span class="ico-oculto"><?= icon('eye-off') ?></span>
                    <span class="ico-visivel"><?= icon('eye') ?></span>
                </button>
            <?php endif; ?>
            <?php if ($sufixo): ?><span class="campo__sufixo"><?= e($sufixo) ?></span><?php endif; ?>
        </div>
        <?php if ($erro): ?><p class="campo__erro" id="erro-<?= e($id) ?>"><?= e($erro) ?></p><?php endif; ?>
    </div>
    <?php
}
