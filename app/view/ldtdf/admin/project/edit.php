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

    <label>
        <?= lang('DEPLOY_SCRIPT') ?>
        <ul>
            <li>
                <span><?= lang('TYPE') ?></span>
                <span class="stub"></span>
                <select name="script_type">
                    <option>
                        -- <?= lang('SELECT_DEPLOY_SCRIPT_TYPE') ?> --
                    </option>
                    <option value="local"><?= lang('LOCAL')  ?></option>
                    <option value="remote"><?= lang('REMOTE')  ?></option>
                </select>   
            </li>
            <li>
                <span><?= lang('PATH') ?></span>
                <span class="stub"></span>
                <input
                placeholder="<?= lang('SERVER_ABSOLUTE_PATH') ?>"
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
