<?= $this->layout('main') ?>
<?= $this->title([lang('USER_MANAGE'), lang('LDTDFMS')]) ?>

<br>

<?php if (isset($task) && is_object($task) && $task) { ?>
<form action="" method="POST">
    <label><?= lang('TASK_TITLE') ?>
        <input type="text" name="title" value="<?= $task->title ?>">
    </label>
    <br>
    <label><?= lang('TASK_STATUS') ?>
        <input type="text" name="status" value="<?= $task->status ?>">
    </label>
    <br><br>
    <input type="submit" value="<?= lang('UPDATE') ?>">
</form>
<?php } ?>
