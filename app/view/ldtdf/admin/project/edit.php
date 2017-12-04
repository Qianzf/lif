<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $project,
    'key'   => 'PROJECT',
    'route' => '/dep/admin/projects',
]) ?>

<form method="POST" autocomplete="off">
    <?= csrf_feild() ?>
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
        <span class="label-title"><?= L('API_TOKEN') ?></span>
        <input
        placeholder="<?= L('REPO_API_TOKEN') ?>"
        type="password"
        name="token"
        value="<?= $project->token ?>">
    </label>

    <label>
        <span class="label-title"><?= L('DEPLOY_SCRIPT') ?></span>
        <ul>
            <ol>
                <span class="label-title"><?= L('TYPE') ?></span>
                <select name="script_type">
                    <option>
                        -- <?= L('SELECT_DEPLOY_SCRIPT_TYPE') ?> --
                    </option>
                    <option value="local"><?= L('LOCAL')  ?></option>
                    <option value="remote"><?= L('REMOTE')  ?></option>
                </select>   
            </ol>
            <ol>
                <span class="label-title"><?= L('PATH') ?></span>
                <input
                placeholder="<?= L('SERVER_ABSOLUTE_PATH') ?>"
                type="text"
                name="script_path"
                value="<?= $project->script ?>">
            </ol>
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
