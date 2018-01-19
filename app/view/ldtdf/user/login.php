<?= $this->layout('login') ?>
<?= $this->title($title = ldtdf('USER_LOGIN')) ?>

<h4><?= $title ?></h4>

<form method="POST">
    <?= csrf_feild() ?>
    
    <input value="<?= ($_REQUEST['lang'] ?? null) ?>"
    type="hidden" name="lang">

    <label>
        <span class="label-title-sm"><?= L('ACCOUNT') ?></span>
        <input type="text" name="account" required>
    </label>
    <label>
        <span class="label-title-sm"><?= L('PASSWORD') ?></span>
        <input type="password" name="passwd" required>
    </label>

    <label>
        <span class="label-title-sm"></span>
        <input type="checkbox" name="remember">
        <sup><small><i><?= L('REMEMBER_ME') ?></i></small></sup>
    </label>

    <label>
        <span class="label-title-sm"></span>
        <button type="submit"><?= L('SIGNIN') ?></button>
    </label>
</form>