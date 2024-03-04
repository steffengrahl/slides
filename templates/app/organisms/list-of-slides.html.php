<?php if (isset($page['flashMessage'])) : ?>
<div class="flash-message<?= ' ' . $page['flashMessage']['level'] ?>">
  <?= $page['flashMessage']['message'] ?>
</div>
<?php endif ?>
<div class="slides-list">
    <?php if (($presentations ?? []) !== []) : ?>
    <?php foreach ($presentations as $presentation) : ?>
    <?php include __DIR__ . '/../molecule/slide-preview-box.html.php' ?>
    <?php endforeach ?>
    <?php endif ?>
    <section class="slide-preview-box">
        <header class="slide-preview-header">
            <h2 class="slide-preview-title">
                Add new slide
            </h2>
        </header>
      <div class="slide-preview-content">
        <a href="/index.php?action=create" class="btn btn-add"><span class="icon">&#43;</span></a>
      </div>
    </section>
</div>