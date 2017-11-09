<?= $this->layout('main') ?>
<?= $this->title([lang('USER_PROFILE'), lang('LDTDFMS')]) ?>
<?= $this->section('back2list', [
    'model' => collect([
        'id' => true,
    ]),
    'key'   => 'PROFILE',
    'route' => '/dep',
]) ?>

<form method="POST">
    <label><?= lang('NAME') ?>
        <input type="text" name="name" value="<?= $name ?>" required>
    </label> 

    <label><?= lang('EMAIL') ?>
        <input type="email" name="email" value="<?= $email ?>" required>
    </label> 

    <label><?= lang('OLD_PASSWORD') ?>
        <input type="password" name="passwordOld"
        placeholder="<?= lang('PROVIDE_OLD_PASSWD') ?>">
    </label> 
    <label><?= lang('NEW_PASSWORD') ?>
        <input type="password" name="passwordNew"
        placeholder="<?= lang('PROVIDE_NEW_PASSWD') ?>">
    </label> 

    <label>
        <input type="submit" value="<?= lang('UPDATE') ?>">
    </label>
</form>
