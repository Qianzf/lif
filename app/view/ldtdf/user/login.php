<?= $this->layout('login') ?>
<?= $this->title([lang('USER_LOGIN'), lang('LDTDFMS')]) ?>

<form action="/dep/user/login" method="POST">
    
    <?= $lang = $_REQUEST['lang'] ?? null ?>
    <?php if ($lang) { ?>
    <input type="hidden" name="lang" value="<?= $lang ?>">
    <?php } ?>

    <label><?= lang('ACCOUNT') ?>
        <input type="text" name="account" required>
    </label><br><br>
    <label><?= lang('PASSWORD') ?>
        <input type="password" name="passwd" required>
    </label><br><br>

    <label><?= lang('REMEMBER_ME') ?>
        <input type="checkbox" name="remember">
    </label><br><br>

    <input type="submit" value="<?= lang('SIGNIN') ?>">
</form>
