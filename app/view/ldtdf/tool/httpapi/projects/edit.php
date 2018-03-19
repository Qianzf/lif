<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $project,
    'key'   => 'PROJECT',
    'route' => lrn('tool/httpapi'),
]) ?>


<form method="POST" autocomplete="off">
    <?= csrf_field() ?>

    <label>
        <span class="label-title"><?= L('TITLE') ?></span>
        <input type="text" name="name" required value="<?= $project->name ?>">
    </label>

    <label>
        <span class="label-title"><?= L('DESCRIPTION') ?></span>
        <textarea name="desc"><?= $project->desc ?></textarea>
    </label>

    <?= $this->section('submit', [
        'model' => $project,
    ]) ?>
</form>
