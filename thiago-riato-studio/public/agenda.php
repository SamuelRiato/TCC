<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();

$hoje = (int) date('N');

app_top('Agenda', 'agenda');
?>
<header class="page-head">
    <div>
        <h1>Agenda</h1>
        <p class="sub">Sua rotina de treinos da semana.</p>
    </div>
</header>

<section class="card bloco" aria-label="Treinos da semana">
    <ul class="lista lista--agenda">
        <?php foreach (DIAS as $n => $nome): ?>
            <?php $t = AGENDA[$n] ?? null; ?>
            <li class="<?= $n === $hoje ? 'is-hoje' : '' ?><?= $t ? '' : ' is-livre' ?>">
                <strong><?= e($nome) ?><?= $n === $hoje ? ' (hoje)' : '' ?></strong>
                <?php if ($t): ?>
                    <span><?= e($t['treino']) ?></span>
                    <span class="hora"><?= icon('clock', 14) ?><?= e($t['hora']) ?></span>
                <?php else: ?>
                    <span>Descanso</span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php app_bottom(); ?>
