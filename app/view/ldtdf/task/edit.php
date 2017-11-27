<?= $this->layout('main') ?>

<?php if (isset($task) && is_object($task)) { ?>
<?php $tid = $task->isAlive() ? $task->id : 'new'; ?>

<?= $this->section('back2list', [
    'model'  => $task,
    'key'    => 'TASK',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/tasks',
]) ?>

<form method="POST" action="/dep/tasks/<?= $tid ?>">
    <?= csrf_feild() ?>

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

    <label>
        <span class="label-title"><?= lang('NOTES') ?></span>
        <div
        id="task-notes"
        class="editormd editormd-vertical">
            <textarea
            class="editormd-markdown-textarea"
            placeholder="<?= lang('TASK_NOTES') ?>"
            name="notes"><?= $task->notes ?></textarea>
        </div>
    </label>

    <?php if ($editable) : ?>
    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
    <?php endif ?>
</form>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    var EditorMDObjects = [
    {
        id : 'task-notes',
        placeholder : "<?=
            lang('NOTES'),
            ' / ',
            lang('ATTACHMENT'),
            lang('ETC')
        ?>"
    }
    ]
    $(function() {
        tryDisplayEditormd()
    })
</script>
<?php } ?>
