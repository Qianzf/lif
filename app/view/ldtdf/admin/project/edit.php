<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $project,
    'key'   => 'PROJECT',
    'route' => '/dep/admin/projects',
]) ?>

<form method="POST" autocomplete="off">
    <?= csrf_feild() ?>
    <label>
        <?= lang('TITLE') ?>
        <input type="text" name="name" required value="<?= $project->name ?>">
    </label>

    <label>
        <?= lang('TYPE') ?>
        <select name="type" required>
            <option
            value="web"
            <?= ($project->type == 'web') ? 'selected' : '' ?>>
            Web
            </option>
            <option
            value="app"
            <?= ($project->type == 'app') ? 'selected' : '' ?>>
            App
            </option>
        </select>
    </label>

    <label>
        <?= lang('REPO_URL') ?>
         <input type="text" name="url" required value="<?= $project->url ?>">
    </label>

    <label>
        <?= lang('VCS') ?>
        <select name="vcs" required>
            <option value="git">git</option>
        </select>
    </label>

    <label>
        <?= lang('DESCRIPTION') ?>
        <textarea name="desc"><?= $project->desc ?></textarea>
    </label>

    <label>
        <?= lang('REPO_API_TOKEN') ?>
         <input type="password" name="token" value="<?= $project->token ?>">
    </label>

    <?= $this->section('submit', [
        'model' => $project,
    ]) ?>
</form>
