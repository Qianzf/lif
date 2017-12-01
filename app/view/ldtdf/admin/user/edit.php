<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $user,
    'key'   => 'USER',
    'route' => '/dep/admin/users',
]) ?>

<form method="POST" autocomplete="off">
    <?= csrf_feild() ?>
    <label><?= L('ACCOUNT') ?>
        <input type="text" name="account" value="<?= $user->account ?>">
    </label>
    <label><?= L('NAME') ?>
        <input type="text" name="name" value="<?= $user->name ?>">
    </label>
    <label><?= L('EMAIL') ?>
        <input type="email" name="email" value="<?= $user->email ?>">
    </label>
    <label><?= L('PASSWORD') ?>
        <input type="password" name="passwd"
        placeholder="<?= L('PROVIDE_NEW_PASSWD') ?>">
    </label>

    <label><?= L('USER_ROLE') ?>
        <select name="role">
            <?php foreach (share('system-roles') as $role) { ?>
            <?php $selected = ($role == $user->role) ? 'selected' : '' ?>
            <option value="<?= $role ?>" <?= $selected ?>>
                <?= L("ROLE_{$role}") ?>
            </option>
            <?php } ?>
        </select>
    </label>
    
    <?= $this->section('submit', [
        'model' => $user,
    ]) ?>
</form>
