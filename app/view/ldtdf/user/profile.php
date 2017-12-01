<?= $this->layout('main') ?>
<?= $this->title([L('USER_PROFILE'), L('LDTDFMS')]) ?>
<?= $this->section('back2list', [
    'model' => collect([
        'id' => true,
    ]),
    'key'   => 'PROFILE',
    'route' => '/dep',
]) ?>

<form method="POST">
    <?= csrf_feild() ?>
    
    <label><?= L('NAME') ?>
        <input type="text" name="name" value="<?= $name ?>" required>
    </label> 

    <label><?= L('EMAIL') ?>
        <input type="email" name="email" value="<?= $email ?>" required>
    </label> 

    <label><?= L('OLD_PASSWORD') ?>
        <input type="password" name="passwordOld"
        placeholder="<?= L('PROVIDE_OLD_PASSWD') ?>">
    </label> 
    <label><?= L('NEW_PASSWORD') ?>
        <input type="password" name="passwordNew"
        placeholder="<?= L('PROVIDE_NEW_PASSWD') ?>">
    </label> 

    <label>
        <input type="submit" value="<?= L('UPDATE') ?>">
    </label>
</form>
