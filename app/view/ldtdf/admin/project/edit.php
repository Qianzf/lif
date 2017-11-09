<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $project,
    'key'   => 'PROJECT',
    'route' => '/dep/admin/projects',
]) ?>

<form method="POST" autocomplete="off">
    <label>
        <?= lang('TITLE') ?>
        <input type="text" name="name" required value="<?= $project->name ?>">
    </label>

    <label>
        <?= 'URL' ?>
         <input type="text" name="url" required value="<?= $project->url ?>">
    </label>

    <label>
        <?= 'VCS' ?>
        <select name="vcs" required>
            <option value="git">git</option>
        </select>
    </label>

    <label>
        <?= lang('DESCRIPTION') ?>
        <textarea name="desc"><?= $project->desc ?></textarea>
    </label>

    <?= $this->section('submit', [
        'model' => $project,
    ]) ?>
</form>
