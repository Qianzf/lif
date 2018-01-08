<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $env,
    'key'   => 'ENV',
    'route' => lrn('admin/envs'),
]) ?>

<form method="POST">
    <?= csrf_feild() ?>

    <label>
        <span class="label-title"><?= L('HOST_ADDR') ?></span>
        <input type="text" name="host" value="<?= $env->host ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('ENV_TYPE') ?></span>
        <select name="type" required>
            <?php if (($types = share('env-types')) && iteratable($types)) : ?>
            <?php foreach ($types as $type) : ?>
            <option
            <?php if ($type == $env->type) : ?>
            selected
            <?php endif ?>
            value="<?= $type ?>">
                <?= L($type) ?>
            </option>
            <?php endforeach; ?>
            <?php endif ?>
        </select>
    </label>

    <label>
        <span class="label-title"><?= L('RELATED_PROJECT') ?></span>
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
        <span class="label-title"><?= L('RELATED_SERVER') ?></span>
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
        <span class="label-title"><?= L('PATH') ?></span>
        <input
        type="text"
        name="path"
        placeholder="<?= L('SERVER_ABSOLUTE_PATH') ?>"
        value="<?= $env->path ?>"
        required>
    </label>

    <label>
        <span class="label-title"><?= L('STATUS') ?></span>
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

    <label>
        <span class="label-title"><?= L('DESCRIPTION') ?></span>
        <textarea name="desc"><?= $env->desc ?></textarea>
    </label>

    <?= $this->section('submit', [
        'model' => $env,
    ]) ?>
</form>
