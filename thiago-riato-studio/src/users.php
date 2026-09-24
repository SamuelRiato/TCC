<?php
declare(strict_types=1);

/* ---------- Armazenamento em JSON (troque por MySQL quando for para produção) ---------- */

function users_read(): array
{
    if (!is_file(STORAGE_FILE)) {
        return [];
    }
    $fh = fopen(STORAGE_FILE, 'r');
    if (!$fh) {
        return [];
    }
    flock($fh, LOCK_SH);
    $raw = stream_get_contents($fh);
    flock($fh, LOCK_UN);
    fclose($fh);
    $dados = json_decode($raw ?: '[]', true);
    return is_array($dados) ? $dados : [];
}

/** Lê, altera e grava sob lock exclusivo. $fn recebe o array de usuários por referência. */
function users_mutate(callable $fn)
{
    $dir = dirname(STORAGE_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $fh = fopen(STORAGE_FILE, 'c+');
    flock($fh, LOCK_EX);
    $raw = stream_get_contents($fh);
    $users = json_decode($raw ?: '[]', true);
    if (!is_array($users)) {
        $users = [];
    }
    $resultado = $fn($users);
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($fh);
    flock($fh, LOCK_UN);
    fclose($fh);
    return $resultado;
}

function user_find(string $id): ?array
{
    return users_read()[$id] ?? null;
}

function user_find_by_email(string $email): ?array
{
    $email = mb_strtolower(trim($email));
    foreach (users_read() as $u) {
        if (($u['email'] ?? '') === $email) {
            return $u;
        }
    }
    return null;
}

function user_create(array $dados): ?array
{
    return users_mutate(function (array &$users) use ($dados) {
        foreach ($users as $u) {
            if (($u['email'] ?? '') === $dados['email']) {
                return null;
            }
        }
        $id = bin2hex(random_bytes(8));
        $users[$id] = $dados + [
            'id'         => $id,
            'criado_em'  => date('c'),
            'nascimento' => '',
            'peso'       => '',
            'altura'     => '',
            'objetivo'   => OBJETIVOS[0],
            'avatar'     => '',
            'checkins'   => [],
        ];
        return $users[$id];
    });
}

function user_update(string $id, array $campos): void
{
    users_mutate(function (array &$users) use ($id, $campos) {
        if (isset($users[$id])) {
            $users[$id] = array_merge($users[$id], $campos);
        }
    });
}

/* ---------- Sessão ---------- */

function current_user(): ?array
{
    $id = $_SESSION['uid'] ?? null;
    return is_string($id) ? user_find($id) : null;
}

function require_login(): array
{
    $u = current_user();
    if (!$u) {
        unset($_SESSION['uid']);
        redirect('login.php');
    }
    return $u;
}

function guest_only(): void
{
    if (current_user()) {
        redirect('inicio.php');
    }
}

function login_user(array $u): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = $u['id'];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $c = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $c['path'], $c['domain'], $c['secure'], $c['httponly']);
    }
    session_destroy();
}

/* ---------- Treinos e progresso ---------- */

function inicio_da_semana(): string
{
    return date('Y-m-d', strtotime('-' . ((int) date('N') - 1) . ' days'));
}

function contar_semana(array $checkins): int
{
    $ini = inicio_da_semana();
    return count(array_filter($checkins, fn($d) => is_string($d) && $d >= $ini));
}

/** Próximo treino da agenda a partir de agora. */
function proximo_treino(): ?array
{
    $agora = new DateTimeImmutable('now');
    for ($i = 0; $i < 8; $i++) {
        $dia = $agora->modify("+$i day");
        $n   = (int) $dia->format('N');
        if (!isset(AGENDA[$n])) {
            continue;
        }
        $hora = AGENDA[$n]['hora'];
        if ($i === 0 && $agora->format('H:i') >= $hora) {
            continue;
        }
        return [
            'treino' => AGENDA[$n]['treino'],
            'hora'   => $hora,
            'hoje'   => $i === 0,
            'quando' => $i === 0 ? 'Hoje' : ($i === 1 ? 'Amanhã' : DIAS[$n]),
        ];
    }
    return null;
}
