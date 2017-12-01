<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $project,
    'key'   => 'PROJECT',
    'route' => '/dep/admin/projects',
]) ?>

<form method="POST" autocomplete="off">
    <?= csrf_feild() ?>
    <label>
        <?= L('TITLE') ?>
        <input type="text" name="name" required value="<?= $project->name ?>">
    </label>

    <label>
        <?= L('TYPE') ?>
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
        <?= L('REPO_URL') ?>
         <input type="text" name="url" required value="<?= $project->url ?>">
    </label>

    <label>
        <?= L('VCS') ?>
        <select name="vcs" required>
            <option value="git">git</option>
        </select>
    </label>

    <label>
        <?= L('DESCRIPTION') ?>
        <textarea name="desc"><?= $project->desc ?></textarea>
    </label>

    <label>
        <?= L('REPO_API_TOKEN') ?>
        <input type="password" name="token" value="<?= $project->token ?>">
    </label>

    <label>
        <?= L('DEPLOY_SCRIPT') ?>
        <ul>
            <li>
                <span><?= L('TYPE') ?></span>
                <span class="stub"></span>
                <select name="script_type">
                    <option>
                        -- <?= L('SELECT_DEPLOY_SCRIPT_TYPE') ?> --
                    </option>
                    <option value="local"><?= L('LOCAL')  ?></option>
                    <option value="remote"><?= L('REMOTE')  ?></option>
                </select>   
            </li>
            <li>
                <span><?= L('PATH') ?></span>
                <span class="stub"></span>
                <input
                placeholder="<?= L('SERVER_ABSOLUTE_PATH') ?>"
                type="text"
                name="script_path"
                value="<?= $project->script ?>">
            </li>
        </ul>
    </label>

    <?= $this->section('submit', [
        'model' => $project,
    ]) ?>
</form>

<script type="text/javascript">
    $('select[name="script_type"]').change(function () {
        console.log(this.value)
        if (this.value) {
            $('input[name="script_path"]').prop('required', true)
        } else {
            $('input[name="script_path"]').removeAttribute('required')
        }
    })
</script>
