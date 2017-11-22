<?php if (isset($users) && iteratable($users)) : ?>
<?php $uid = intval($_GET['user'] ?? 0); ?>
<p>
    <button class="btn-info">
        <?= lang('SPECIFIC_USER') ?>
    </button>
    <select name="user" class="query-filters">
        <option value="0" <?= (-1 === $uid) ? 'selected' : '' ?>>
            <?= lang('ALL') ?>
        </option>
        <?php foreach ($users as $user) : ?>
        <option <?= ($user->id == $uid) ? 'selected' : '' ?>
        value="<?= $user->id ?>">
            <?= $user->name ?>
            <?php if ($user->id == share('user.id')) : ?>
            (<?= lang('ME') ?>)
            <?php endif ?>
        </option>
        <?php endforeach ?>
    </select>
</p>
<?php endif ?>
