<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();

app_top('Treinos', 'treinos');
?>
<header class="page-head">
    <div>
        <h1>Treinos</h1>
        <p class="sub">Suas fichas e atividades de cardio.</p>
    </div>
</header>

<div class="treinos">
    <?php foreach (TREINOS as $chave => $t): ?>
        <section class="card bloco" id="<?= e($chave) ?>" aria-labelledby="t-<?= e($chave) ?>">
            <div class="bloco__cabeca">
                <span class="card__icone"><?= icon($t['icone']) ?></span>
                <div>
                    <h2 id="t-<?= e($chave) ?>"><?= e($t['titulo']) ?></h2>
                    <p class="sub"><?= e($t['descricao']) ?></p>
                </div>
            </div>
            <ul class="lista">
                <?php foreach ($t['itens'] as [$nome, $detalhe]): ?>
                    <li><strong><?= e($nome) ?></strong><span><?= e($detalhe) ?></span></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endforeach; ?>
</div>
<?php app_bottom(); ?>
