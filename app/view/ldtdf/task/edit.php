<?= $this->layout('main') ?>

<?php if (isset($task) && is_object($task)) { ?>
<?php $tid   = $task->isAlive() ? $task->id : 'new'; ?>

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
        <input type="hidden" name="story" value="<?= $story->id ?>">
        <?= $this->section('instant-search', [
            'api' => '/dep/tasks/stories/attachable',
            'oldVal' => $story->title,
            'sresKeyInput' => 'story',
            // 'sresKey' => 'id',
            'sresVal' => 'title',
        ]) ?>
    </label>

    <label>
        <span class="label-title">
            <?= lang('RELATED_PROJECT') ?>
        </span>
        <select name="project" required>
            <option>-- <?= lang('SELECT_PROJECT') ?> --</option>
            <?php foreach ($projects as $proj): ?>
                <option
                <?php if ($project->id == $proj->id): ?>
                    selected
                <?php endif ?>
                value="<?= $proj->id ?>">
                    <?= $proj->name, " ($proj->type)" ?>
                </option>
            <?php endforeach ?>
        </select>
    </label>

    <label>
        <span class="label-title"><?= lang('TASK_NOTES') ?></span>
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
