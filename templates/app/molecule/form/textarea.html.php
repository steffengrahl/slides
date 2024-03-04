<label>
    <div>
        <?= $field['label'] ?><?php if ($field['required']) : ?>*<?php endif ?>
    </div>
    <textarea name="<?= $name ?>"><?= $field['value'] ?></textarea>
    <?php if ($field['error'] !== '') : ?>
        <p class="error">
            <?= $field['error'] ?>
        </p>
    <?php endif ?>
</label>