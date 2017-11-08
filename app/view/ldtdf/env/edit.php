<?php $action = lang(($env->id ? 'EDIT' : 'ADD').'_ENV') ?>

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
            <a href="/dep/admin/envs"><?= lang('BACK_TO_LIST') ?></a>
        </small>
    </sup>
</h4>

<form method="POST">
    <label>
        <?= lang('TITLE') ?>
        <input type="text" name="name" value="<?= $env->name ?>" required>
    </label><br>

    <label>
        <?= lang('HOST_ADDR') ?>
        <input type="text" name="host" value="<?= $env->host ?>" required>
    </label><br>

    <label>
        <?= lang('ENV_TYPE') ?>
        <select name="type" required>
            <?php if (($types = share('env-types')) && iteratable($types)) { ?>
            <?php foreach ($types as $type) { ?>
            <option
            <?php if ($type == $env->type) { ?>
            selected
            <?php } ?>
            value="<?= $type ?>">
                <?= lang($type) ?>
            </option>
            <?php } ?>
            <?php } ?>
        </select>
    </label><br>

    <label>
        <?= lang('RELATED_SERVER') ?>
        <select name="server" required>
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
    </label><br>

    <br>
    <input value="<?= lang($env->id ? 'UPDATE' : 'CREATE') ?>"
    type="submit">
</form>
