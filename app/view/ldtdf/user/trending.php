<?= $this->layout('main') ?>
<?= $this->title([lang('TRENDING'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>
<?= $this->section('filter/user') ?>

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
        <?= $trending->at, ' , ', lang("ROLE_{$user->role}") ?>
        <a href="/dep/user/<?= $user->id ?>">
            <?= $user->name ?>
        </a>
        <?= lang($trending->event), $trending->genHTMLStringOfEvent() ?>
    </li>
</ul>
<?php endforeach ?>
<?php endif ?>

<?= $this->section('pagebar') ?>
