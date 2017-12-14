<?= $this->layout('main') ?>
<?= $this->title([L('HOMEPAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="admin/projects">
            <button><?= L('PROJECT_MANAGE') ?></button>
        </a>
    </dd>
    <dd>
        <a href="admin/envs">
            <button><?= L('ENVIRONMENT_MANAGE') ?></button>
        </a>
    </dd>
    <dd>
        <a href="admin/servers">
            <button><?= L('SERVER_MANAGE') ?></button>
        </a>
    </dd>
</dl>
