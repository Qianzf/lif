<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $user,
    'key'   => 'USER',
    'route' => lrn('admin/users'),
]) ?>

<form method="POST" autocomplete="off">
    <?= csrf_field() ?>
    <label>
        <span class="label-title"><?= L('ACCOUNT') ?></span>
        <input type="text" name="account" value="<?= $user->account ?>">
    </label>
    <label>
        <span class="label-title"><?= L('NAME') ?></span>
        <input type="text" name="name" value="<?= $user->name ?>">
    </label>
    <label>
        <span class="label-title"><?= L('EMAIL') ?></span>
        <input type="email" name="email" value="<?= $user->email ?>">
    </label>
    <label>
        <span class="label-title"><?= L('PASSWORD') ?></span>
        <input type="password" name="passwd"
        placeholder="<?= L('PROVIDE_NEW_PASSWD') ?>">
    </label>

    <label>
        <span class="label-title"><?= L('USER_ROLE') ?></span>
        <select name="role">
            <?php foreach (share('system-roles') as $role) { ?>
            <?php $selected = ($role == $user->role) ? 'selected' : '' ?>
            <option value="<?= $role ?>" <?= $selected ?>>
                <?= L("ROLE_{$role}") ?>
            </option>
            <?php } ?>
        </select>
    </label>

    <label>
        <span class="label-title"><?= L('ABILITY_SUMMARY') ?></span>
        <input type="text" name="ability" value="<?= $user->ability ?>">
    </label>

    <label>
        <span class="label-title"><?= L('STATUS') ?></span>
        <?= L('NORMAL') ?>
        <input type="radio" name="status" value="1" checked>
        <?= L('DISABLE') ?>
        <input type="radio" name="status" value="0"
        <?= $user->status ? '' : 'checked' ?>>
    </label>
    
    <?= $this->section('submit', [
        'model' => $user,
    ]) ?>
</form>
