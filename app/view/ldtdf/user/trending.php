<?= $this->layout('main') ?>
<?= $this->title([lang('TRENDING'), lang('LDTDFMS')]) ?>
<?= $this->section('search') ?>

<?php if (isset($trending) && $trending) { ?>
<?php foreach ($trending as $log) { ?>
<ul>
    <li>
        <?php
            $name = (isset($admin) && $admin)
            ? (
                (($user = $log->user()) && is_object($user))
                ? (
                    (share('__USER.id') == $user->id)
                    ? lang('YOU') : $user->name
                ) : lang('UNKNOWN_USER')
            )
            : lang('YOU');
        ?>
        <?= $log->at.' , '.$name.' '.lang($log->event).' '.$log->detail ?>
    </li>
</ul>
<?php } ?>
<?php } ?>

<?= $this->section('pagebar') ?>
