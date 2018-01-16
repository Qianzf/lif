<?= $this->layout('main') ?>
<?= $this->title(ldtdf('PROJECT_INFO')) ?>
<?= $this->section('common') ?>
<?= $this->section('title', [
    'key' => 'PROJECT_INFO'
]) ?>

<div class="form">
    <label>
        <button class="btn-info">
            <?= L('TITLE') ?>
        </button>
        <span class="stub"></span>
        <small><?= $project->name ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= L('TYPE') ?>
        </button>
        <span class="stub"></span>
        <small><?= L($project->type) ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= L('DESCRIPTION') ?>
        </button>
        <span class="stub"></span>
        <small><?= $project->desc ?></small>
    </label>

    <?php if (ci_equal(share('user.role'), 'dev')): ?>
    <label>
        <button class="btn-info">
            <?= L('REPO') ?>
        </button>
        <span class="stub"></span>
        <code><?= $project->url ?></code>
    </label>
    <?php endif ?>
</div>

<div class="vertical"></div>
