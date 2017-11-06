<?= $this->layout('main') ?>
<?= $this->title([lang('HOMEPAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<ul>
    <li>
        <a href="admin/users"><?= lang('USER_MANAGE') ?></a>
    </li>
    <li>
        <a href="admin/projects"><?= lang('PROJECT_MANAGE') ?></a>
    </li>
    <li>
        <a href="admin/envs"><?= lang('ENVIRONMENT_MANAGE') ?></a>
    </li>
</ul>
