<?= $this->layout('main') ?>
<?= $this->title([lang('HOMEPAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<ul>
    <li>
        <a href="admin/users"><?= lang('USER_MANAGE') ?></a>
    </li>
    <li>项目配置</li>
    <li>环境管理</li>
</ul>
