<section class="slide-preview-box">
  <header class="slide-preview-header">
    <h2 class="slide-preview-title">
      <a href="?presentation=<?= urlencode($presentation->getFileName()) ?>">
          <?= $presentation->getTitle() ?>
      </a>
    </h2>
  </header>
  <?php if ($presentation->getImagePath() !== null) : ?>
  <figure class="slide-preview-image">
    <img src="<?= $presentation->getImagePath() ?>" alt="Slide Vorschau">
  </figure>
  <?php endif ?>
</section>
