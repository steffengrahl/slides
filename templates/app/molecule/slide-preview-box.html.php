<section class="slide-preview-box">
  <header class="slide-preview-header">
    <h2 class="slide-preview-title">
      <a href="?presentation=<?= urlencode($presentation->getFileName()) ?>" target="_blank">
          <?= $presentation->getTitle() ?>
      </a>
    </h2>
  </header>
  <figure class="slide-preview-image">
    <?php if ($presentation->getImagePath() !== null) : ?>
    <img src="<?= $presentation->getImagePath() ?>" alt="Slide Vorschau">
    <?php else : ?>
    <img src="img/app/no-image.jpg" alt="Slide Vorschau">
    <?php endif ?>
  </figure>
</section>
