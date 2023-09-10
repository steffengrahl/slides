<main>
  <section class="overview">
    <header>
      <h2>
        All your presentations
      </h2>
    </header>
    <div class="presentations">
        <?php if ( $presentations !== []) : ?>
        <?php foreach ($presentations as $presentation) : ?>
        <div class="card">
          <a href="?presentation=<?= urlencode($presentation->getFileName()) ?>">
              <?= $presentation->getTitle() ?>
          </a>
        </div>
        <?php endforeach ?>
        <?php else : ?>
        <p>
          Didn't find any presentations. Let´s add one. 🙂
        </p>
        <?php endif ?>
    </div>
  </section>
</main>