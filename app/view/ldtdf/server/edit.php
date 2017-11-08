<?php $action = lang(($server->id ? 'EDIT' : 'ADD').'_SERVER') ?>

<?= $this->layout('main') ?>
<?= $this->title([
        $action,
        lang('LDTDFMS')
    ])
?>

<h4>
    <?= $action ?>
    <sup>
        <small>
            <a href="/dep/admin/servers"><?= lang('BACK_TO_LIST') ?></a>
        </small>
    </sup>
</h4>

<form method="POST">
    <label>
        <?= lang('TITLE') ?>
        <input type="text" name="name" value="<?= $server->name ?>" required>
    </label><br>

    <label>
        <?= lang('HOST_ADDR') ?>
        <input type="text" name="host" value="<?= $server->host ?>" required>
    </label><br>

    <label>
        <?= lang('PORT') ?>
        <input type="number" name="port" value="<?= $server->port ?>" required>
    </label><br>

    <br>
    <input value="<?= lang($server->id ? 'UPDATE' : 'CREATE') ?>"
    type="submit">
</form>
