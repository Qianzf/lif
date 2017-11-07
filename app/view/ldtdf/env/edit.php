<?= $this->layout('main') ?>
<?= $this->title([
        lang(($server->id ? 'EDIT' : 'ADD').'_SERVER'),
        lang('LDTDFMS')
    ])
?>

<br>
<form method="POST">
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
