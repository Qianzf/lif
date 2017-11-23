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

<form method="POST" novalidate>
    <?= csrf_feild() ?>

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
            required
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
        required
        placeholder="<?= lang('USER_ROLE') ?>"
        value="<?= $task->story_role ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_WHAT') ?></span>
        <textarea
        <?= $editStyle ?>
        required
        placeholder="<?= lang('WHAT_FUNCTIONALITIES') ?>"
        name="story_activity"><?= $task->story_activity ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_FOR') ?></span>
        <textarea
        <?= $editStyle ?>
        required
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
            class="editormd-markdown-textarea"
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

    <?php if ($task->status) : ?>
    <button class="btn-info">
        <?= lang("TASK_{$task->status}") ?>
    </button>
    <?php endif ?>

    <?php if ($editable) : ?>
    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
    <?php endif ?>
</form>
<?php } ?>

<?= css([
    'editor.md/css/editormd.min',
    'editor.md/css/editormd.preview.min',
    'editor.md/lib/codemirror/codemirror.min',
    'editor.md/lib/codemirror/addon/dialog/dialog',
    'editor.md/lib/codemirror/addon/search/matchesonscrollbar',
]) ?>

<?= js([
    'editor.md/editormd.min',
    'editor.md/lib/codemirror/codemirror.min',
    'editor.md/lib/codemirror/modes.min',
    'editor.md/lib/codemirror/addons.min',
    'editor.md/lib/marked.min',
    'editor.md/lib/prettify.min',
]) ?>

<script type="text/javascript">
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
})
<?php endif ?>
</script>
