<?= $this->layout('main') ?>
<?= $this->title([lang('PROJECT_INFO'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>
<?= $this->section('title', [
    'key' => 'PROJECT_INFO'
]) ?>

<div class="form">
    <label>
        <button class="btn-info">
            <?= lang('TITLE') ?>
        </button>
        <span class="stub"></span>
        <small><?= $project->name ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= lang('TYPE') ?>
        </button>
        <span class="stub"></span>
        <small><?= lang($project->type) ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= lang('DESCRIPTION') ?>
        </button>
        <span class="stub"></span>
        <small><?= $project->desc ?></small>
    </label>    
</div>

<div class="vertical"></div>
