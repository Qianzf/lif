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
    </label>

    <label>
        <?= lang('WHETHER_CUSTOM_TASK_DETAILS') ?>
        <input type="radio" name="custom" value="no" checked>
        <?= lang('NO') ?>
        <input type="radio" name="custom" value="yes">
        <?= lang('YES') ?>
    </label>

    <label class="custom-task-attr invisible-default"><?= lang('STATUS') ?>
        <input type="text" name="status" value="<?= $task->status ?>">
    </label>

    <label class="outer-task-detail"><?= lang('TASK_URL') ?>
        <input type="text" name="url" value="<?= $task->url ?>">
    </label>

    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
</form>
<?php } ?>
