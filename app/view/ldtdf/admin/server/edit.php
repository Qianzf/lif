<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $server,
    'key'   => 'SERVER',
    'route' => '/dep/admin/servers',
]) ?>

<form method="POST">
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

    <?= $this->section('submit', [
        'model' => $server,
    ]) ?>
</form>
