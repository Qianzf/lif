<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $server,
    'key'   => 'SERVER',
    'route' => '/dep/admin/servers',
]) ?>

<form method="POST">
    <?= csrf_feild() ?>
    <label>
        <?= L('TITLE') ?>
        <input type="text" name="name" value="<?= $server->name ?>" required>
    </label>

    <label>
        <?= L('HOST_ADDR') ?>
        <input type="text" name="host" value="<?= $server->host ?>" required>
    </label>

    <label>
        <?= L('PORT') ?>
        <input type="number" name="port" value="<?= $server->port ?>" required>
    </label>

    <label>
        <?= L('USER') ?>
        <input type="text" name="user" value="<?= $server->user ?>" required>
    </label>

    <label>
        <?= L('PUBLIC_KEY') ?>
        <input
        type="text"
        name="pubk"
        placeholder="<?= L('SERVER_ABSOLUTE_PATH') ?>"
        value="<?= $server->pubk ?>" required>
    </label>

    <label>
        <?= L('PRIVATE_KEY') ?>
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
