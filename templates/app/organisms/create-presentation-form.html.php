<form action="index.php?action=create" method="post" class="slides-create-presentation">
  <div class="group">
      <?php foreach (($form['fields'] ?? []) as $name => $field) : ?>
      <?php include __DIR__ . '/../molecule/form/input-field.html.php' ?>
      <?php endforeach ?>
  </div>
  <div class="group group-buttons">
    <button type="reset" class="btn">
      Cancel
    </button>
    <button type="submit" name="btn-create" value="1" class="btn btn-cta">
      Create
    </button>
  </div>
</form>