<?php $name  = $name ?? null; ?>
<?php $query = $_GET[$name] ?? null; ?>

<?php if ($displayTitle ?? false): ?>
<button class="btn-info">
    <?= L("SPECIFIC_{$name}") ?>
</button>
<?php endif ?>

<select name="<?= $name ?>" class="query-filters">
    <option value="-1"><?= L("ALL_{$name}") ?></option>
    <?php if (iteratable($list ?? null)): ?>
    <?php foreach ($list as $key => $val) : ?>
    <?php $_val = ($kval ?? false) ? $val : $key; ?>
    <option
    <?= ci_equal($query, $_val) ? 'selected' : '' ?>
    value="<?= $_val ?>">
        <?php if ($vlang ?? null) : ?>
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

<?php unset(
    $this->data['vlang'],
    $this->data['kval'],
    $this->data['isUser']
    );
?>
