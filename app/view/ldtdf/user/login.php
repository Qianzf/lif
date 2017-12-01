<?php $title = L('USER_LOGIN').' - '.L('LDTDFMS'); ?>
<?= $this->layout('login') ?>
<?= $this->title($title) ?>

<h4>
    <?= $title ?>
</h4>

<form method="POST">
    <?= csrf_feild() ?>
    
    <input value="<?= ($_REQUEST['lang'] ?? null) ?>"
    type="hidden" name="lang">

    <label><?= L('ACCOUNT') ?>
        <input type="text" name="account" required>
    </label>
    <label><?= L('PASSWORD') ?>
        <input type="password" name="passwd" required>
    </label>

    <label><?= L('REMEMBER_ME') ?>
        <input type="checkbox" name="remember">
    </label>

    <label>
        <input type="submit" value="<?= L('SIGNIN') ?>">
    </label>
</form>
