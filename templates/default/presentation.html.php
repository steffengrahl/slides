<main>
    <?php if (empty($page['content'])) : ?>
    <?php include __DIR__ . '/error.html.php' ?>
    <?php else : ?>
    <section class="slide title">
        <h1><?= $page['title'] ?? 'Title not found' ?></h1>
    </section>
    <?php foreach ($page['content'] as $key => $slide) : ?>
    <section class="slide">
        <header>
            <h2>
                <?= $slide['title'] ?? '' ?>
            </h2>
            <div class="slide-number">
                <?= ($key ?? 0) + 2 ?>/<?= $page['slideCount'] ?? 0 ?>
            </div>
        </header>
        <div class="slide-content">
            <?= $slide['content'] ?? '' ?>
        </div>
        <footer>
            <?= $page['title'] ?? '' ?>
        </footer>
    </section>
    <?php endforeach; ?>
    <?php endif; ?>
</main>