<?= $this->layout('main') ?>

<?= $this->section('back2list', [
    'model' => $task,
    'key'   => 'TASK',
    'route' => '/dep/tasks',
]) ?>

<?php if (isset($task) && is_object($task)) { ?>
<form method="POST">
    <label><?= lang('TITLE') ?>
        <input type="text" name="title" value="<?= $task->title ?>">
        <br><br>
    </label>

    <?= lang('WHETHER_CUSTOM_TASK_DETAILS') ?>
    <label>
        <input type="radio" name="custom" value="no" checked>
        <?= lang('NO') ?>
    </label>
    <label>
        <input type="radio" name="custom" value="yes">
        <?= lang('YES') ?>
    </label>
    <br><br>

    <label class="custom-task-attr invisible-default"><?= lang('STATUS') ?>
        <input type="text" name="status" value="<?= $task->status ?>">
        <br>
    </label>

    <label class="outer-task-detail"><?= lang('URL') ?>
        <input type="text" name="url" value="<?= $task->url ?>">
        <br>
    </label>

    <br>
    <input type="submit"
    value="<?= lang($task->id ? 'UPDATE' : 'CREATE') ?>">
</form>
<?php } ?>
