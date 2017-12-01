<?= $this->layout('main') ?>
<?= $this->title([L('PROJECT_INFO'), L('LDTDFMS')]) ?>
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
</div>

<div class="vertical"></div>
