<?= $this->layout('main') ?>
<?= $this->title([lang('USER_PROFILE'), lang('LDTDFMS')]) ?>

<form method="POST" action="/dep/user/profile">
    <label><?= lang('PASSWORD') ?>:
        <input type="password" name="password">
    </label> <br>

    <br>

    <label><?= lang('EMAIL') ?>:
        <input type="email" name="email" value="<?= $email ?>" required>
    </label> <br>

    <br>

    <input type="submit" value="<?= lang('UPDATE') ?>">
</form>
