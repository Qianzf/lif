<?= $this->layout('login') ?>
<?= $this->title($title = ldtdf('USER_LOGIN')) ?>

<h4><?= $title ?></h4>

<form method="POST">
    <?= csrf_feild() ?>
    
    <input value="<?= ($_REQUEST['lang'] ?? null) ?>"
    type="hidden" name="lang">

    <label>
        <span class="label-title"><?= L('ACCOUNT') ?></span>
        <input type="text" name="account" required>
    </label>
    <label>
        <span class="label-title"><?= L('PASSWORD') ?></span>
        <input type="password" name="passwd" required>
    </label>

    <label>
        <span class="label-title">
            <i><small><?= L('REMEMBER_ME') ?></small></i>
        </span>
        <input type="checkbox" name="remember">
    </label>

    <label>
        <span class="label-title">
            <input type="submit" value="<?= L('SIGNIN') ?>">
        </span>
    </label>
</form>
