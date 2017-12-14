<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model'  => $story,
    'key'    => 'STORY',
    'route'  => '/dep/stories',
]) ?>

<?php if (isset($story) && is_object($story)) { ?>
<?php $sid = $story->alive() ? $story->id : 'new'; ?>

<form method="POST" action="/dep/stories/<?= $sid ?>">
    <?= csrf_feild() ?>

    <label>
        <span class="label-title">* <?= L('TITLE') ?></span>
        <input
        type="text"
        name="title"
        required
        placeholder="<?= L('STORY_TITLE') ?>"
        value="<?= $story->title ?>">
    </label>

    <label>
        <span class="label-title">* <?= L('STORY_WHO') ?></span>
        <input
        type="text"
        name="role"
        required
        placeholder="<?= L('WHAT_USER_ROLE') ?>"
        value="<?= $story->role ?>">
    </label>

    <label>
        <span class="label-title">* <?= L('STORY_WHAT') ?></span>
        <textarea
        required
        placeholder="<?= L('WHAT_FUNCTIONALITIES') ?>"
        name="activity"><?= $story->activity ?></textarea>
    </label>

    <label>
        <span class="label-title">* <?= L('STORY_FOR') ?></span>
        <textarea
        required
        placeholder="<?= L('ACHIEVE_WHAT_VALUE') ?>"
        name="value"><?= $story->value ?></textarea>
    </label>

    <label>
        <span class="label-title">* <?= L('STORY_AC') ?></span>
        <textarea
        placeholder="<?= L('STORY_AC_STUB') ?>"
        name="acceptances"><?= $story->acceptances ?></textarea>
    </label>

    <label>
        <span class="label-title"><?= L('REMARKS') ?></span>
        <div
        id="story-others"
        class="editormd editormd-vertical">
            <textarea
            class="editormd-markdown-textarea"
            name="extra"><?= $story->extra ?></textarea>
        </div>
    </label>

    <?php if ($editable ?? false) : ?>
    <?= $this->section('submit', [
        'model' => $story
    ]) ?>
    <?php endif ?>
</form>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    var EditorMDObjects = [
    {
        id : 'story-others',
        placeholder : "<?= L('NOTES'), ' / ', L('ATTACHMENT_ETC') ?>"
    }
    ]
    $(function() {
        tryDisplayEditormd()
    })
</script>
<?php } ?>
