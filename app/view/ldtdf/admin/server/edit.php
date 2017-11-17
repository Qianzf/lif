<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $server,
    'key'   => 'SERVER',
    'route' => '/dep/admin/servers',
]) ?>

<form method="POST">
    <?= csrf_feild() ?>
    <label>
        <?= lang('TITLE') ?>
        <input type="text" name="name" value="<?= $server->name ?>" required>
    </label>

    <label>
        <?= lang('HOST_ADDR') ?>
        <input type="text" name="host" value="<?= $server->host ?>" required>
    </label>

    <label>
        <?= lang('PORT') ?>
        <input type="number" name="port" value="<?= $server->port ?>" required>
    </label>

    <label>
        <?= lang('USER') ?>
        <input type="text" name="user" value="<?= $server->user ?>" required>
    </label>

    <label>
        <?= lang('PUBLIC_KEY') ?>
        <input
        type="text"
        name="pubk"
        placeholder="<?= lang('SERVER_ABSOLUTE_PATH') ?>"
        value="<?= $server->pubk ?>" required>
    </label>

    <label>
        <?= lang('PRIVATE_KEY') ?>
        <input
        type="text"
        name="prik"
        placeholder="<?= lang('SERVER_ABSOLUTE_PATH') ?>"
        value="<?= $server->prik ?>" required>
    </label>

    <?= $this->section('submit', [
        'model' => $server,
    ]) ?>
</form>
