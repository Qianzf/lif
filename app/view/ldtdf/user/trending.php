<?= $this->layout('main') ?>
<?= $this->title([lang('TRENDING'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>
<?php $uid = intval($_GET['user'] ?? 0); ?>

<p>
    <button class="btn-info">
        <?= lang('SPECIFIC_USER') ?>
    </button>
    <select name="user" class="query-filters">
        <option value="0" <?= (-1 === $uid) ? 'selected' : '' ?>>
            <?= lang('ALL') ?>
        </option>

        <?php if (isset($users) && iteratable($users)) { ?>
        <?php foreach ($users as $user) { ?>
        <option <?= ($user->id == $uid) ? 'selected' : '' ?>
        value="<?= $user->id?>">
            <?= $user->name ?>
            <?php if ($user->id == share('user.id')) { ?>
            (<?= lang('ME') ?>)
            <?php } ?>
        </option>
        <?php } ?>
        <?php } ?>
    </select>
</p>

<?php if (isset($trending) && iteratable($trending)) { ?>
<?php foreach ($trending as $log) { ?>
<ul>
    <li>
        <?php
            $user = $log->user();
            $name = (share('user.id') == $user->id)
            ? lang('YOU') : (
                $user->name ?? lang('UNKNOWN_USER')
            );
        ?>
        <?= $log->at ,' , ' ,lang($user->role) ?>
        <a href="/dep/user/<?= $user->id ?>">
            <?= $user->name ?>
        </a>
        <?= lang($log->event), (
            $log->detail
            ? (': '.$log->detail)
            : ''
        )?>
    </li>
</ul>
<?php } ?>
<?php } ?>

<?= $this->section('pagebar') ?>
