<?= $this->layout('main') ?>

<?php if (isset($task) && is_object($task)) { ?>
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

<form method="POST">
    <?= csrf_feild() ?>

    <label>
        <span class="label-title">
            <?= lang('TASK_STATUS') ?>
        </span>
        <?php if ($task->status) : ?>
        <code><?= lang("TASK_{$task->status}") ?></code>
        <?php endif ?>
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
        <?= $editStyle ?>
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
        <?= $editStyle ?>
        type="text"
        name="story_role"
        class="required"
        placeholder="<?= lang('WHAT_USER_ROLE') ?>"
        value="<?= $task->story_role ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_WHAT') ?></span>
        <textarea
        <?= $editStyle ?>
        class="required"
        placeholder="<?= lang('WHAT_FUNCTIONALITIES') ?>"
        name="story_activity"><?= $task->story_activity ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_FOR') ?></span>
        <textarea
        <?= $editStyle ?>
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
            <?= $editStyle ?>
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
            <?= $editStyle ?>
            placeholder="<?= lang('OTHERS') ?>"
            name="extra"><?= $task->extra ?></textarea>
        </div>
    </label>

    <?php if ($editable) : ?>
    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
    <?php endif ?>

    <label><button id="assign-to">
        <?= lang('ASSIGN') ?>
    </button></label>
</form>
<?php } ?>

<?= $this->section('lib/editormd') ?>

<script type="text/javascript">
    $('#assign-to').click(function (e) {
        e.preventDefault()
    })
    var EditorMDObjects = [
    {
        id : 'task-others',
        placeholder : "<?= lang('OTHER_NOTES') ?>"
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
