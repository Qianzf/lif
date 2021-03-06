<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $server,
    'key'   => 'SERVER',
    'route' => lrn('admin/servers'),
]) ?>

<form method="POST">
    <?= csrf_feild() ?>
    <label>
        <span class="label-title"><?= L('TITLE') ?></span>
        <input type="text" name="name" value="<?= $server->name ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('LOCATION') ?></span>
        <select name="location">
            <option
            <?php if (ci_equal($server->location, 'remote')): ?>
            selected
            <?php endif ?>
            value="remote"><?= L('REMOTE') ?></option>
            <option
            <?php if (ci_equal($server->location, 'local')): ?>
            selected
            <?php endif ?>
            value="local"><?= L('LOCAL') ?></option>
        </select>
    </label>

    <label>
        <span class="label-title"><?= L('HOST_ADDR') ?></span>
        <input
        type="text"
        name="host"
        placeholder="<?= L('ENSURE_ADDR_ACCESSIBLE') ?>"
        value="<?= $server->host ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('PORT') ?></span>
        <input type="number" name="port" value="<?= $server->port ?: 22 ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('USER') ?></span>
        <input type="text" name="user" value="<?= $server->user ?: 'root' ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('PUBLIC_KEY') ?></span>
        <input
        type="text"
        name="pubk"
        placeholder="<?= L('SERVER_ABSOLUTE_PATH') ?>"
        value="<?= $server->pubk ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('PRIVATE_KEY') ?></span>
        <input
        type="text"
        name="prik"
        placeholder="<?= L('SERVER_ABSOLUTE_PATH') ?>"
        value="<?= $server->prik ?>" required>
    </label>

    <?= $this->section('submit', [
        'model' => $server,
    ]) ?>
</form>
