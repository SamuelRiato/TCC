f<?php
require __DIR__ . '/../src/bootstrap.php';
$u = require_login();

$prox           = proximo_treino();
$feitos         = min(META_SEMANAL, contar_semana($u['checkins'] ?? []));
$confirmadoHoje = in_array(date('Y-m-d'), $u['checkins'] ?? [], true);
$podeConfirmar  = $prox && $prox['hoje'] && !$confirmadoHoje;
$letras         = ['S', 'T', 'Q', 'Q', 'S'];

app_top('Início', 'inicio');
?>
<header class="page-head">
    <div class="saudacao">
        <?= avatar_html($u, 'avatar avatar--md') ?>
        <div>
            <h1>Olá, <?= e(primeiro_nome($u['nome'])) ?>!</h1>
            <p>Foco na sua meta de hoje 💪</p>
        </div>
    </div>
    <button type="button" class="icon-btn icon-btn--borda" aria-label="Notificações"><?= icon('bell') ?></button>
</header>

<div class="inicio-grid">
    <section class="hero" aria-labelledby="prox-titulo">
        <?php if ($prox): ?>
            <div class="hero__topo">
                <span class="tag">Próximo treino</span>
                <span class="hero__quando"><?= e($prox['quando'] . ' às ' . $prox['hora']) ?></span>
            </div>
            <h2 id="prox-titulo"><?= e($prox['treino']) ?></h2>
            <p class="hero__prof">Acompanhamento com <strong>Prof. Thiago Riato</strong></p>
            <button type="button" class="btn btn--sm" data-confirmar-presenca <?= $podeConfirmar ? '' : 'disabled' ?>
                    <?= (!$podeConfirmar && !$confirmadoHoje) ? 'aria-describedby="nota-presenca"' : '' ?>>
                <span data-rotulo><?= $confirmadoHoje ? 'Presença confirmada' : 'Confirmar Presença' ?></span><?= icon('check', 16) ?>
            </button>
            <?php if (!$podeConfirmar && !$confirmadoHoje): ?>
                <p class="hero__nota" id="nota-presenca">A confirmação abre no dia do treino.</p>
            <?php endif; ?>
        <?php else: ?>
            <h2 id="prox-titulo">Nenhum treino agendado</h2>
            <p class="hero__prof">Fale com o Prof. Thiago Riato para montar sua agenda.</p>
        <?php endif; ?>
    </section>

    <section class="card progresso" aria-labelledby="prog-titulo" data-meta="<?= META_SEMANAL ?>">
        <h2 id="prog-titulo">Progresso Semanal</h2>
        <p class="progresso__resumo" data-progresso-texto><?= $feitos ?>/<?= META_SEMANAL ?> treinos concluídos</p>
        <div class="barra" role="progressbar" aria-label="Treinos concluídos na semana"
             aria-valuemin="0" aria-valuemax="<?= META_SEMANAL ?>" aria-valuenow="<?= $feitos ?>" data-barra>
            <span style="width: <?= ($feitos / META_SEMANAL) * 100 ?>%"></span>
        </div>
        <ul class="dias">
            <?php foreach ($letras as $i => $letra): ?>
                <li class="dia<?= $i < $feitos ? ' is-feito' : '' ?>">
                    <span class="dia__marca"><?= icon('check', 14) ?></span>
                    <span class="dia__letra"><?= $letra ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="meus-treinos" aria-labelledby="mt-titulo">
        <h2 id="mt-titulo">Meus Treinos</h2>
        <div class="grid-cards">
            <?php foreach (TREINOS as $chave => $t): ?>
                <a class="card card--link" href="treinos.php#<?= e($chave) ?>">
                    <span class="card__icone"><?= icon($t['icone']) ?></span>
                    <span class="card__texto"><strong><?= e($t['titulo']) ?></strong><small><?= e($t['descricao']) ?></small></span>
                    <?= icon('chevron', 16) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?php app_bottom(); ?>
