<?= $this->layout('login') ?>
<?= $this->title([sysmsg('USER_LOGIN'), sysmsg('LDTDFMS')]) ?>

<form action="/dep/user/login" method="POST">
    
    <?= $lang = $_REQUEST['lang'] ?? null ?>
    <?php if ($lang) { ?>
    <input type="hidden" name="lang" value="<?= $lang ?>">
    <?php } ?>

    <label><?= sysmsg('ACCOUNT') ?>
        <input type="text" name="account" required>
    </label>
    <label><?= sysmsg('PASSWORD') ?>
        <input type="password" name="passwd" required>
    </label>

    <input type="submit" value="<?= sysmsg('SIGNIN') ?>">
</form>
