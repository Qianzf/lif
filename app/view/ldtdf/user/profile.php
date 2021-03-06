<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => collect([
        'id' => true,
    ]),
    'key'   => 'USER_PROFILE',
    'route' => lrn(),
]) ?>

<form method="POST">
    <?= csrf_feild() ?>
    
    <label>
        <span class="label-title"><?= L('NAME') ?></span>
        <input type="text" name="name" value="<?= $name ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('EMAIL') ?></span>
        <input type="email" name="email" value="<?= $email ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('OLD_PASSWORD') ?></span>
        <input type="password" name="pswdold"
        placeholder="<?= L('PROVIDE_OLD_PASSWD') ?>">
    </label>

    <label>
        <span class="label-title"><?= L('NEW_PASSWORD') ?></span>
        <input type="password" name="pswdnew"
        placeholder="<?= L('PROVIDE_NEW_PASSWD') ?>">
    </label>

    <label>
        <span class="label-title">
            <input type="submit" class="btn-default"
            value="<?= L('UPDATE') ?>">
        </span>
    </label>
</form>
