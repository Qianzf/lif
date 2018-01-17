<?php $name  = $name ?? null; ?>
<?php $query = $_GET[$name] ?? null; ?>

<select name="<?= $name ?>" class="query-filters">
    <option value="-1"><?= L("ALL_{$name}") ?></option>
    <?php if (iteratable($list ?? null)): ?>
    <?php foreach ($list as $key => $val) : ?>
    <?php $_val = ($kval ?? false) ? $val : $key; ?>
    <option <?= (strtolower($_val) == strtolower($query)) ? 'selected' : '' ?>
    value="<?= $_val ?>">

        <?php if (($vlang ?? false) !== false) : ?>
        <?= L("{$vlang}_{$val}") ?>
        <?php else: ?>
        <?= $this->escape($val) ?>
        <?php endif ?>

        <?php if (($isUser ?? false) && ($key == share('user.id'))) : ?>
        (<?= L('ME') ?>)
        <?php endif ?>

    </option>
    <?php endforeach ?>
    <?php endif ?>
</select>