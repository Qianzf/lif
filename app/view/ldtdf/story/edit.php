<?= $this->layout('main') ?>
<?= $this->title([lang('BUG_LIST'), lang('LDTDFMS')]) ?>

<?= $this->section('back2list', [
    'model'  => $story,
    'key'    => 'STORY',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/stories',
]) ?>

<?php if (isset($story) && is_object($story)) { ?>
<?php $custom = ('yes' == $story->custom); ?>
<?php $checkedNo  = $custom ? '' : 'checked'; ?>
<?php $checkedYes = $custom ? 'checked' : ''; ?>
<?php $hiddenOuterDetail = $custom ? 'invisible-default' : ''; ?>
<?php $hiddenCustomAttr  = $custom ? '' : 'invisible-default'; ?>
<?php $sid = $story->isAlive() ? $story->id : 'new' ?>

<form method="POST" action="/dep/stories/<?= $sid ?>">
    <?= csrf_feild() ?>

    <?php if ($story->status) : ?>
    <label>
        <span class="label-title">
            <?= lang('STORY_STATUS') ?>
        </span>
        <code><?= lang("STORY_{$story->status}") ?></code>
    </label>
    <?php endif ?>

    <label>
        <span class="label-title">
            <?= lang('WHETHER_CUSTOM_STORY') ?>
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
        placeholder="<?= lang('STORY_TITLE') ?>"
        value="<?= $story->title ?>">
    </label>

    <label class="outer-task-detail <?= $hiddenOuterDetail ?>">
        <span class="label-title"><?= lang('STORY_URL') ?></span>
        <?php if ($editable) : ?>
            <input
            type="text"
            name="url"
            class="required"
            placeholder="<?= lang('STORY_DETAILS_URL') ?>"
            value="<?= $story->url ?>">
        <?php else: ?>
            <a href="<?= $story->url ?>">
                <?= $story->url ?>
            </a>
        <?php endif ?>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_WHO') ?></span>
        <input
        type="text"
        name="role"
        class="required"
        placeholder="<?= lang('WHAT_USER_ROLE') ?>"
        value="<?= $story->story_role ?>">
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_WHAT') ?></span>
        <textarea
        class="required"
        placeholder="<?= lang('WHAT_FUNCTIONALITIES') ?>"
        name="activity"><?= $story->story_activity ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_FOR') ?></span>
        <textarea
        class="required"
        placeholder="<?= lang('ACHIEVE_WHAT_VALUE') ?>"
        name="value"><?= $story->story_value ?></textarea>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('STORY_AC') ?></span>
        <div
        id="story-acceptances"
        class="editormd editormd-vertical custom-task-attr">
            <textarea
            style="display:none"
            class="editormd-markdown-textarea required"
            placeholder="<?= lang('STORY_AC') ?>"
            name="acceptances"><?= $story->acceptances ?></textarea>
        </div>
    </label>

    <label class="custom-task-attr <?= $hiddenCustomAttr ?>">
        <span class="label-title"><?= lang('OTHER_NOTES') ?></span>
        <div
        id="story-others"
        class="editormd editormd-vertical custom-task-attr">
            <textarea
            style="display:none"
            class="editormd-markdown-textarea"
            placeholder="<?= lang('OTHERS') ?>"
            name="extra"><?= $story->extra ?></textarea>
        </div>
    </label>

    <?php if ($editable) : ?>
    <?= $this->section('submit', [
        'model' => $story
    ]) ?>
    <?php endif ?>
</form>

<?= $this->section('trendings-with-sort', [
    'model' => $story,
]) ?>
<?= $this->section('lib/editormd') ?>

<script type="text/javascript">
    var EditorMDObjects = [
    {
        id : 'story-others',
        placeholder : "<?=
            lang('OTHER_NOTES'),
            ' / ',
            lang('ATTACHMENT'),
            lang('ETC')
        ?>"
    },
    {
        id : 'story-acceptances',
        placeholder : "<?= lang('STORY_AC') ?>"
    }
    ]
    <?php if ($story->isAlive()) : ?>
    $(function() {
        tryDisplayEditormd()
        removeRequired()
    })
    <?php endif ?>
</script>
<?php } ?>
