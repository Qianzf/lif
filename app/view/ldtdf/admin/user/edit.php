<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $user,
    'key'   => 'USER',
    'route' => '/dep/admin/users',
]) ?>

<form method="POST" autocomplete="off">
    <label><?= lang('ACCOUNT') ?>
        <input type="text" name="account" value="<?= $user->account ?>">
    </label>
    <label><?= lang('NAME') ?>
        <input type="text" name="name" value="<?= $user->name ?>">
    </label>
    <label><?= lang('EMAIL') ?>
        <input type="email" name="email" value="<?= $user->email ?>">
    </label>
    <label><?= lang('PASSWORD') ?>
        <input type="password" name="passwd"
        placeholder="<?= lang('PROVIDE_NEW_PASSWD') ?>">
    </label>
    <label><?= lang('USER_ROLE') ?>
        <select name="role">
        
            <?php foreach (share('system-roles') as $role) { ?>
            <?php $selected = ($role == $user->role) ? 'selected' : '' ?>
            <option value="<?= $role ?>" <?= $selected ?>>
                <?= lang($role) ?>
            </option>
            <?php } ?>

        </select>
    </label>
    
    <?= $this->section('submit', [
        'model' => $user,
    ]) ?>
</form>
