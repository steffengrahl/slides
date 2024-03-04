<section>
  <header>
    <h2>
      Edit <?= $presentation->getTitle() ?>
    </h2>
  </header>
  <form action="index.php?action=edit&presentation=<?= urlencode($presentation->getFileName()) ?>" method="post" class="edit-presentation">
    <div class="group">
      <?php foreach (($form['fields'] ?? []) as $name => $field) : ?>
      <?php include __DIR__ . '/../molecule/form/textarea.html.php' ?>
      <?php endforeach ?>
    </div>
    <div class="group group-buttons">
      <button type="reset" class="btn">
        Cancel
      </button>
      <button type="submit" name="btn-update" value="1" class="btn btn-cta">
        Update
      </button>
    </div>
  </form>
</section>