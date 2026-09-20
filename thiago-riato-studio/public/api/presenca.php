<?php
require __DIR__ . '/../../src/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

function resposta(int $codigo, array $dados): void
{
    http_response_code($codigo);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resposta(405, ['ok' => false, 'erro' => 'Método não permitido.']);
}
$u = current_user();
if (!$u) {
    resposta(401, ['ok' => false, 'erro' => 'Faça login para continuar.']);
}
if (!csrf_valid($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    resposta(403, ['ok' => false, 'erro' => 'Sessão expirada. Recarregue a página.']);
}
$prox = proximo_treino();
if (!$prox || !$prox['hoje']) {
    resposta(409, ['ok' => false, 'erro' => 'Não há treino agendado para agora.']);
}

$hoje  = date('Y-m-d');
$total = users_mutate(function (array &$users) use ($u, $hoje) {
    $checkins = $users[$u['id']]['checkins'] ?? [];
    if (!in_array($hoje, $checkins, true)) {
        $checkins[] = $hoje;
    }
    $users[$u['id']]['checkins'] = $checkins;
    return contar_semana($checkins);
});

resposta(200, ['ok' => true, 'total' => min(META_SEMANAL, $total), 'meta' => META_SEMANAL]);
