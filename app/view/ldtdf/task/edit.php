<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $task,
    'key'   => 'TASK',
    'route' => '/dep/tasks',
]) ?>

<?php if (isset($task) && is_object($task)) { ?>
<?php $hiddenCustomRadio = $task->id ? 'invisible-default' : ''; ?>
<?php $custom = ('yes' == $task->custom); ?>
<?php $checkedNo  = $custom ? '' : 'checked'; ?>
<?php $checkedYes = $custom ? 'checked' : ''; ?>
<?php $hiddenOuterDetail = $custom ? 'invisible-default' : ''; ?>
<?php $hiddenCustomAttr  = $custom ? '' : 'invisible-default'; ?>
<form method="POST">
    <?= csrf_feild() ?>

    <label>
        <?= lang('TITLE') ?>
        <input type="text" name="title" value="<?= $task->title ?>">
    </label>

    <label class="<?= $hiddenCustomRadio ?>">
        <?= lang('WHETHER_CUSTOM_TASK_DETAILS') ?>
        <input
        <?= $checkedNo ?>
        type="radio"
        name="custom"
        value="no">
        <?= lang('NO') ?>
        <input
        <?= $checkedYes ?>
        type="radio"
        name="custom"
        value="yes">
        <?= lang('YES') ?>
    </label>

    <label class="outer-task-detail <?= $hiddenOuterDetail ?>">
        <?= lang('TASK_URL') ?>
        <input type="text" name="url" value="<?= $task->url ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('STORY_WHO') ?>
        <input
        type="text"
        name="story_role"
        value="<?= $task->story_role ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('STORY_WHAT') ?>
        <input
        type="text"
        name="story_activity"
        value="<?= $task->story_activity ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('STORY_FOR') ?>
        <input
        type="text"
        name="story_value"
        value="<?= $task->story_value ?>">
    </label>

    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
</form>
<?php } ?>
