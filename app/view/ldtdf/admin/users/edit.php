<?= $this->layout('main') ?>

<?= $this->section('back2list', [
    'model' => $user,
    'key'   => 'USER',
    'route' => '/dep/admin/users',
]) ?>


<form method="POST" autocomplete="off">
    <label><?= lang('ACCOUNT') ?>：
        <input type="text" name="account" value="<?= $user->account ?>">
    </label> <br>
    <label><?= lang('NAME') ?>：
        <input type="text" name="name" value="<?= $user->name ?>">
    </label> <br>
    <label><?= lang('EMAIL') ?>：
        <input type="email" name="email" value="<?= $user->email ?>">
    </label> <br>
    <label><?= lang('PASSWORD') ?>：
        <input type="password" name="passwd"
        placeholder="<?= lang('PROVIDE_NEW_PASSWD') ?>">
    </label> <br>
    <label><?= lang('USER_ROLE') ?>：
        <select name="role">
        
            <?php foreach (share('system-roles') as $role) { ?>
            <?php $selected = ($role == $user->role) ? 'selected' : '' ?>
            <option value="<?= $role ?>" <?= $selected ?>>
                <?= lang($role) ?>
            </option>
            <?php } ?>

        </select>
    </label> <br>
    
    <input type="submit"
    value="<?php echo $user->id ? lang('UPDATE') : lang('CREATE'); ?>">

    <?php if ($user->id) { ?>
    <a href="/dep/admin/users/delete/<?= $user->id ?>">
        <input type="button" value="<?= lang('DELETE') ?>">
    </a>
    <?php } ?>
</form>
