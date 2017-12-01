<?php if (isset($users) && iteratable($users)) : ?>
<?php $uid = intval($_GET['user'] ?? 0); ?>
<?php $user = $user ?? 'USER' ?>
<p>
    <button class="btn-info">
        <?= L("SPECIFIC_{$user}") ?>
    </button>
    <select name="user" class="query-filters">
        <option value="0" <?= (-1 === $uid) ? 'selected' : '' ?>>
            <?= L('ALL') ?>
        </option>
        <?php foreach ($users as $user) : ?>
        <option <?= ($user->id == $uid) ? 'selected' : '' ?>
        value="<?= $user->id ?>">
            <?= $user->name ?>
            <?php if ($user->id == share('user.id')) : ?>
            (<?= L('ME') ?>)
            <?php endif ?>
        </option>
        <?php endforeach ?>
    </select>
</p>
<?php endif ?>
