<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $env,
    'key'   => 'ENV',
    'route' => '/dep/admin/envs',
]) ?>

<form method="POST">
    <?= csrf_feild() ?>
    <label>
        <?= L('TITLE') ?>
        <input type="text" name="name" value="<?= $env->name ?>" required>
    </label>

    <label>
        <?= L('HOST_ADDR') ?>
        <input type="text" name="host" value="<?= $env->host ?>" required>
    </label>

    <label>
        <?= L('PATH') ?>
        <input
        type="text"
        name="path"
        placeholder="<?= L('SERVER_ABSOLUTE_PATH') ?>"
        value="<?= $env->path ?>"
        required>
    </label>

    <label>
        <?= L('ENV_TYPE') ?>
        <select name="type" required>
            <?php if (($types = share('env-types')) && iteratable($types)) { ?>
            <?php foreach ($types as $type) { ?>
            <option
            <?php if ($type == $env->type) { ?>
            selected
            <?php } ?>
            value="<?= $type ?>">
                <?= L($type) ?>
            </option>
            <?php } ?>
            <?php } ?>
        </select>
    </label>

    <label>
        <?= L('RELATED_PROJECT') ?>
        <select name="project" required>
            <option>--<?= L('PLEASE_SELECT_PROJECT') ?>--</option>
            <?php if (isset($projects) && iteratable($projects)) { ?>
            <?php foreach ($projects as $project) { ?>
            <option
            <?php if ($project->id == $env->project) { ?>
            selected
            <?php } ?>
            value="<?= $project->id ?>">
                <?= $project->name ?>
                (<?= $project->url ?>)
            </option>
            <?php } ?>
            <?php } ?>
        </select>
    </label>

    <label>
        <?= L('RELATED_SERVER') ?>
        <select name="server" required>
            <option>--<?= L('PLEASE_SELECT_SERVER') ?>--</option>
            <?php if (isset($servers) && iteratable($servers)) { ?>
            <?php foreach ($servers as $server) { ?>
            <option
            <?php if ($server->id == $env->server) { ?>
            selected
            <?php } ?>
            value="<?= $server->id ?>">
                <?= $server->name ?>
                (<?= $server->host ?>)
            </option>
            <?php } ?>
            <?php } ?>
        </select>
    </label>

    <label>
        <?= L('STATUS') ?>
        <select name="status" required>
            <?php if (($stat = share('env-status')) && iteratable($stat)) { ?>
            <?php foreach ($stat as $status) { ?>
            <option
            <?php if ($status == $env->status) { ?>
            selected
            <?php } ?>
            value="<?= $status ?>">
                <?= L($status) ?>
            </option>
            <?php } ?>
            <?php } ?>
        </select>
    </label>

    <?= $this->section('submit', [
        'model' => $env,
    ]) ?>
</form>
