<?php $title = lang('USER_LOGIN').' - '.lang('LDTDFMS'); ?>
<?= $this->layout('login') ?>
<?= $this->title($title) ?>

<h4>
    <?= $title ?>
</h4>

<form action="/dep/user/login" method="POST">
    <input value="<?= ($_REQUEST['lang'] ?? null) ?>"
    type="hidden" name="lang">

    <label><?= lang('ACCOUNT') ?>
        <input type="text" name="account" required>
    </label>
    <label><?= lang('PASSWORD') ?>
        <input type="password" name="passwd" required>
    </label>

    <label><?= lang('REMEMBER_ME') ?>
        <input type="checkbox" name="remember">
    </label>

    <label>
        <input type="submit" value="<?= lang('SIGNIN') ?>">
    </label>
</form>
