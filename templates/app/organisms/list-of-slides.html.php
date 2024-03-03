<div class="slides-list">
    <?php if ($slides !== []) : ?>
    <?php foreach ($slides as $slide) : ?>
    <?php include __DIR__ . '/../molecule/slide-preview-box.html.php' ?>
    <?php endforeach ?>
    <?php endif ?>
    <section class="slide-preview-box">
        <header class="slide-preview-header">
            <h2 class="slide-preview-title">
                Add new slide
            </h2>
        </header>
        <a href="/index.php?action=create" class="btn btn-add"><span class="icon">&#43;</span></a>
    </section>
</div>