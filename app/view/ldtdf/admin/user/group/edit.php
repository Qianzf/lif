<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $group,
    'key'   => 'GROUP',
    'route' => lrn('admin/users/groups'),
]) ?>

<form method="POST" autocomplete="off">
    <?= csrf_feild() ?>

    <label>
        <?= L('TITLE') ?>
        <input type="text" name="name" value="<?= $group->name ?>" required>
    </label>

    <label>
        <?= L('DESCRIPTION') ?>
        <textarea type="text" name="desc"><?= $group->desc ?></textarea>
    </label>

    <label>
        <?= L('PLEASE_SELECT_USER') ?>
    
        <label>
        <?php if (isset($users) && iteratable($users)) : ?>
        <?php foreach ($users as $user) : ?>
        <input
        <?php if ($user->inGroup($group->id)) : ?>
        checked
        <?php endif ?>
        type="checkbox"
        name="users[]"
        value="<?= $user->id ?>">
        <small><?= $user->name ?></small> <br>
        <?php endforeach ?>
        <?php endif ?>
        </label>
    </label>

    <?= $this->section('submit', [
        'model' => $group,
    ]) ?>
</form>
