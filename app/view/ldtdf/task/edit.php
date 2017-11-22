<?= $this->layout('main') ?>

<?php if (isset($task) && is_object($task)) { ?>
<?php $hiddenCustomRadio = $task->id ? 'invisible-default' : ''; ?>
<?php $custom = ('yes' == $task->custom); ?>
<?php $checkedNo  = $custom ? '' : 'checked'; ?>
<?php $checkedYes = $custom ? 'checked' : ''; ?>
<?php $hiddenOuterDetail = $custom ? 'invisible-default' : ''; ?>
<?php $hiddenCustomAttr  = $custom ? '' : 'invisible-default'; ?>
<?php $editable = ($task->creator == share('user.id')) || !$task->isAlive(); ?>
<?php $editStyle = $editable ? '' : 'disabled'; ?>

<?= $this->section('back2list', [
    'model'  => $task,
    'key'    => 'TASK',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/tasks',
]) ?>

<form method="POST" novalidate>
    <?= csrf_feild() ?>

    <label>
        <?= lang('TITLE') ?>
        <input
        <?= $editStyle ?>
        type="text"
        name="title"
        required
        value="<?= $task->title ?>">
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
        <?php if ($editable) : ?>
            <input
            type="text"
            name="url"
            required
            value="<?= $task->url ?>">
        <?php else: ?>
            <a href="<?= $task->url ?>">
                <?= $task->url ?>
            </a>
        <?php endif ?>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('STORY_WHO') ?>
        <input
        <?= $editStyle ?>
        type="text"
        name="story_role"
        required
        value="<?= $task->story_role ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('STORY_WHAT') ?>
        <textarea
        <?= $editStyle ?>
        required
        name="story_activity"><?= $task->story_activity ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('STORY_FOR') ?>
        <textarea
        <?= $editStyle ?>
        required
        name="story_value"><?= $task->story_value ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('STORY_AC') ?>
        <textarea
        required
        <?= $editStyle ?>
        name="acceptances"><?= $task->acceptances ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <?= lang('ATTACHMENT') ?>
        <textarea
        <?= $editStyle ?>
        name="extra"><?= $task->extra ?></textarea>
    </label>

    <?php if ($editable) : ?>
    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
    <?php endif ?>
</form>
<?php } ?>
