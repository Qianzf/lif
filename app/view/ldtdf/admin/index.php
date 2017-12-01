<?= $this->layout('main') ?>
<?= $this->title([L('HOMEPAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="admin/users"><?= L('USER_MANAGE') ?></a>
        </button>
    </dd>
    <dd>
        <button>
            <a href="admin/projects"><?= L('PROJECT_MANAGE') ?></a>
        </button>
    </dd>
    <dd>
        <button>
            <a href="admin/envs"><?= L('ENVIRONMENT_MANAGE') ?></a>
        </button>
    </dd>
    <dd>
        <button>
            <a href="admin/servers"><?= L('SERVER_MANAGE') ?></a>
        </button>
    </dd>
</dl>
