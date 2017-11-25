<?= $this->layout('main') ?>

<?php if (isset($task) && is_object($task)) { ?>
<?php $custom = ('yes' == $task->custom); ?>
<?php $checkedNo  = $custom ? '' : 'checked'; ?>
<?php $checkedYes = $custom ? 'checked' : ''; ?>
<?php $hiddenOuterDetail = $custom ? 'invisible-default' : ''; ?>
<?php $hiddenCustomAttr  = $custom ? '' : 'invisible-default'; ?>

<?= $this->section('back2list', [
    'model'  => $task,
    'key'    => 'TASK',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/tasks',
]) ?>

<?= $this->section('assign-form', [
    'model' => $task,
    'key'   => 'TASK',
    'route' => "/dep/tasks/{$task->id}/assign"
]) ?>

<form method="POST" action="/dep/tasks/<?= $task->id ?>">
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
        <span class="label-title">
            <?= lang('WHETHER_CUSTOM_TASK_DETAILS') ?>
        </span>
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

    <label>
        <span class="label-title"><?= lang('TITLE') ?></span>
        <input
        type="text"
        name="title"
        required
        placeholder="<?= lang('TASK_TITLE') ?>"
        value="<?= $task->title ?>">
    </label>

    <label class="outer-task-detail <?= $hiddenOuterDetail ?>">
        <span class="label-title"><?= lang('TASK_URL') ?></span>
        <?php if ($editable) : ?>
            <input
            type="text"
            name="url"
            class="required"
            placeholder="<?= lang('TASK_DETAILS_URL') ?>"
            value="<?= $task->url ?>">
        <?php else: ?>
            <a href="<?= $task->url ?>">
                <?= $task->url ?>
            </a>
        <?php endif ?>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_WHO') ?></span>
        <input
        type="text"
        name="story_role"
        class="required"
        placeholder="<?= lang('WHAT_USER_ROLE') ?>"
        value="<?= $task->story_role ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_WHAT') ?></span>
        <textarea
        class="required"
        placeholder="<?= lang('WHAT_FUNCTIONALITIES') ?>"
        name="story_activity"><?= $task->story_activity ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_FOR') ?></span>
        <textarea
        class="required"
        placeholder="<?= lang('ACHIEVE_WHAT_VALUE') ?>"
        name="story_value"><?= $task->story_value ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_AC') ?></span>
        <div
        id="task-acceptances"
        class="editormd editormd-vertical custom-task-attr">
            <textarea
            style="display:none"
            class="editormd-markdown-textarea required"
            placeholder="<?= lang('STORY_AC') ?>"
            name="acceptances"><?= $task->acceptances ?></textarea>
        </div>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('OTHER_NOTES') ?></span>
        <div
        id="task-others"
        class="editormd editormd-vertical custom-task-attr">
            <textarea
            style="display:none"
            class="editormd-markdown-textarea"
            placeholder="<?= lang('OTHERS') ?>"
            name="extra"><?= $task->extra ?></textarea>
        </div>
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
<?= $this->section('lib/editormd') ?>
<?= $this->section('lib/jqueryui') ?>

<script type="text/javascript">
    var EditorMDObjects = [
    {
        id : 'task-others',
        placeholder : "<?=
            lang('OTHER_NOTES'),
            ' / ',
            lang('ATTACHMENT'),
            lang('ETC')
        ?>"
    },
    {
        id : 'task-acceptances',
        placeholder : "<?= lang('STORY_AC') ?>"
    }
    ]
    <?php if ($task->isAlive()) : ?>
    $(function() {
        tryDisplayEditormd()
        removeRequired()
    })
    <?php endif ?>
</script>
<?php } ?>
