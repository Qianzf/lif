<?= $this->layout('main') ?>

<?php if (isset($task) && is_object($task)) { ?>
<?php $tid = $task->isAlive() ? $task->id : 'new' ?>

<?= $this->section('back2list', [
    'model'  => $task,
    'key'    => 'TASK',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/tasks',
]) ?>

<form method="POST" action="/dep/tasks/<?= $tid ?>">
    <?= csrf_feild() ?>

    <?php if ($task->status) : ?>
    <label>
        <span class="label-title">
            <?= lang('TASK_STATUS') ?>
        </span>
        <code><?= lang("TASK_{$task->status}") ?></code>
    </label>
    <?php endif ?>

    <label>
        <span class="label-title">
            <?= lang('RELATED_STORY') ?>
        </span>
        <select name="story" required>
            <option>-- <?= lang('SELECT_STORY') ?> --</option>
            <?php foreach ($stories as $story): ?>
                <option
                <?php if ($task->story == $story->id): ?>
                    selected
                <?php endif ?>
                value="<?= $story->id ?>">
                    <?= $story->title ?>
                </option>
            <?php endforeach ?>
        </select>
    </label>

    <label>
        <span class="label-title">
            <?= lang('RELATED_PROJECT') ?>
        </span>
        <select name="project" required>
            <option>-- <?= lang('SELECT_PROJECT') ?> --</option>
            <?php foreach ($projects as $project): ?>
                <option
                <?php if ($task->project == $project->id): ?>
                    selected
                <?php endif ?>
                value="<?= $project->id ?>">
                    <?= $project->name, " ($project->type)" ?>
                </option>
            <?php endforeach ?>
        </select>
    </label>

    <?php if ($editable) : ?>
    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
    <?php endif ?>
</form>

<?= $this->section('trendings-with-sort', [
    'model' => $task,
]) ?>
<?php } ?>
