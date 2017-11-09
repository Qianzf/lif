<?php share('hidden-search-bar', true) ?>
<?= $this->layout('main') ?>
<?= $this->title([lang('HOMEPAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="admin/users"><?= lang('USER_MANAGE') ?></a>
        </button>
    </dd>
    <dd>
        <button>
            <a href="admin/projects"><?= lang('PROJECT_MANAGE') ?></a>
        </button>
    </dd>
    <dd>
        <button>
            <a href="admin/envs"><?= lang('ENVIRONMENT_MANAGE') ?></a>
        </button>
    </dd>
    <dd>
        <button>
            <a href="admin/servers"><?= lang('SERVER_MANAGE') ?></a>
        </button>
    </dd>
</dl>
