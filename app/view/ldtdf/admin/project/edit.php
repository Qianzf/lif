<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $project,
    'key'   => 'PROJECT',
    'route' => lrn('admin/projects'),
]) ?>

<form method="POST" autocomplete="off">
    <?= csrf_field() ?>
    <label>
        <span class="label-title"><?= L('TITLE') ?></span>
        <input type="text" name="name" required value="<?= $project->name ?>">
    </label>

    <label>
        <span class="label-title"><?= L('TYPE') ?></span>
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
        <span class="label-title"><?= L('REPO_URL') ?></span>
         <input type="text" name="url" required value="<?= $project->url ?>">
    </label>

    <label>
        <span class="label-title"><?= L('REPO_TOKEN') ?></span>
        <input
        placeholder="<?= L('REPO_API_TOKEN') ?>"
        type="password"
        name="token"
        value="<?= $project->token ?>">
    </label>

    <label>
        <span class="label-title"><?= L('VCS') ?></span>
        <select name="vcs" required>
            <option value="git">git</option>
        </select>
    </label>

    <label>
        <span class="label-title"><?= L('DESCRIPTION') ?></span>
        <textarea name="desc"><?= $project->desc ?></textarea>
    </label>

    <label>
        <span class="label-title"><?= L('DEPLOY_SCRIPT') ?></span>
        <input
        placeholder="<?= L('PROJECT_ABSOLUTE_PATH_AND_EXECUTABLE') ?>"
        type="text"
        name="build_script"
        value="<?= $project->build_script ?>">
    </label>

    <label>
        <span class="label-title"><?= L('CONFIGRWAPI') ?></span>
        <ul>
            <li>
                <span><?= L('EXECUTE_ORDER') ?></span>
                <select name="config_order">
                    <option
                    <?php if (ci_equal($project->config_order, 'after')): ?>
                    selected
                    <?php endif ?>
                    value="after">
                        <?= L('AFTER_BUILD_SCRIPT') ?>
                    </option>
                    <option
                    <?php if (ci_equal($project->config_order, 'before')): ?>
                    selected
                    <?php endif ?>
                    value="before">
                        <?= L('BEFORE_BUILD_SCRIPT') ?>
                    </option>
                </select>
            </li>
            <li>
                <span><?= L('PATH'), '/', L('COMMAND') ?></span>
                <input
                placeholder="<?= L('PROJECT_ABSOLUTE_PATH_AND_EXECUTABLE') ?>"
                type="text"
                name="config_api"
                value="<?= $project->config_api ?>">
            </li>
        </ul>
    </label>

    <?= $this->section('submit', [
        'model' => $project,
    ]) ?>
</form>
