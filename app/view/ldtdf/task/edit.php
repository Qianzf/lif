<?= $this->layout('main') ?>

<?= $this->section('back2list', [
    'model' => $task,
    'key'   => 'TASK',
    'route' => '/dep/tasks',
]) ?>

<?php if (isset($task) && is_object($task)) { ?>
<form method="POST">
    <?= lang('WHETHER_CUSTOM') ?>
    <label>
        <input type="radio" name="custom" value="no" checked>
        <?= lang('NO') ?>
    </label>
    <label>
        <input type="radio" name="custom" value="yes">
        <?= lang('YES') ?>
    </label><br>

    <label class="custom-task-attr invisible-default"><?= lang('TITLE') ?>
        <input type="text" name="title" value="<?= $task->title ?>">
    </label><br>

    <label class="custom-task-attr invisible-default"><?= lang('STATUS') ?>
        <input type="text" name="status" value="<?= $task->status ?>">
    </label><br><br>

    <input type="submit"
    value="<?= lang($task->id ? 'UPDATE' : 'CREATE') ?>">
</form>
<?php } ?>
