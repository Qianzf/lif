<?= $this->layout('main') ?>
<?= $this->title([lang('USER_PROFILE'), lang('LDTDFMS')]) ?>

<br>

<form method="POST" action="/dep/user/profile">
    <label><?= lang('OLD_PASSWORD') ?>:
        <input type="password" name="passwordOld"
        placeholder="<?= lang('PROVIDE_OLD_PASSWD') ?>">
    </label> <br>
    <label><?= lang('NEW_PASSWORD') ?>:
        <input type="password" name="passwordNew"
        placeholder="<?= lang('PROVIDE_NEW_PASSWD') ?>">
    </label> <br>

    <br>

    <label><?= lang('EMAIL') ?>:
        <input type="email" name="email" value="<?= $email ?>" required>
    </label> <br>

    <br>

    <input type="submit" value="<?= lang('UPDATE') ?>">
</form>
