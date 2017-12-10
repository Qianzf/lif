<?php $name  = $name ?? null; ?>
<?php $list  = $list ?? null; ?>
<?php $query = $_GET[$name] ?? null; ?>

<select name="<?= $name ?>" class="query-filters">
    <option value="-1"><?= L("ALL_{$name}") ?></option>
    <?php if (iteratable($list)): ?>
    <?php foreach ($list as $key => $item) : ?>
    <option <?= ($item == $query) ? 'selected' : '' ?>
    value="<?= $item ?>">
        <?= L($key) ?>
    </option>
    <?php endforeach ?>
    <?php endif ?>
</select>