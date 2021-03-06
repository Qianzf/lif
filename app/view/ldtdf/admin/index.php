<?= $this->layout('main') ?>
<?= $this->title(ldtdf('HOMEPAGE')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="admin/users">
            <button><?= L('USER_MANAGE') ?></button>
        </a>
    </dd>
    <dd>
        <a href="admin/projects">
            <button><?= L('PROJECT_MANAGE') ?></button>
        </a>
    </dd>
    <dd>
        <a href="admin/servers">
            <button><?= L('SERVER_MANAGE') ?></button>
        </a>
    </dd>
    <dd>
        <a href="admin/envs">
            <button><?= L('ENVIRONMENT_MANAGE') ?></button>
        </a>
    </dd>
</dl>
