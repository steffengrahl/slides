<label>
  <div>
      <?= $field['label'] ?><?php if ($field['required']) : ?>*<?php endif ?>
  </div>
  <input type="text" name="<?= $name ?>">
  <?php if ($field['error'] !== '') : ?>
  <p class="error">
    <?= $field['error'] ?>
  </p>
  <?php endif ?>
</label>