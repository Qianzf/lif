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

        <?php if (isset($users) && iteratable($users)) : ?>
        <?php foreach ($users as $user) : ?>
        <option <?= ($user->id == $uid) ? 'selected' : '' ?>
        value="<?= $user->id ?>">
            <?= $user->name ?>
            <?php if ($user->id == share('user.id')) : ?>
            (<?= lang('ME') ?>)
            <?php endif ?>
        </option>
        <?php endforeach ?>
        <?php endif ?>
    </select>
</p>

<?php if (isset($trendings) && iteratable($trendings)) : ?>
<?php foreach ($trendings as $trending) : ?>
<ul>
    <li>
        <?php
            $user = $trending->user();
            $name = (share('user.id') == $user->id)
            ? lang('YOU') : (
                $user->name ?? lang('UNKNOWN_USER')
            );
        ?>
        <?= $trending->at, ' , ', lang($user->role) ?>
        <a href="/dep/user/<?= $user->id ?>">
            <?= $user->name ?>
        </a>
        <?= lang($trending->event), $trending->genHTMLStringOfEvent() ?>
    </li>
</ul>
<?php endforeach ?>
<?php endif ?>

<?= $this->section('pagebar') ?>
