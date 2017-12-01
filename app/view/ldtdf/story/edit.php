<?= $this->layout('main') ?>
<?= $this->title([L('BUG_LIST'), L('LDTDFMS')]) ?>

<?= $this->section('back2list', [
    'model'  => $story,
    'key'    => 'STORY',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/stories',
]) ?>

<?php if (isset($story) && is_object($story)) { ?>
<?php $sid = $story->isAlive() ? $story->id : 'new'; ?>

<form method="POST" action="/dep/stories/<?= $sid ?>">
    <?= csrf_feild() ?>

    <?php if ($story->status) : ?>
    <label>
        <span class="label-title">
            <?= L('STORY_STATUS') ?>
        </span>
        <code><?= L("STORY_{$story->status}") ?></code>
    </label>
    <?php endif ?>

    <label>
        <span class="label-title"><?= L('TITLE') ?></span>
        <input
        type="text"
        name="title"
        required
        placeholder="<?= L('STORY_TITLE') ?>"
        value="<?= $story->title ?>">
    </label>

    <label>
        <span class="label-title"><?= L('STORY_WHO') ?></span>
        <input
        type="text"
        name="role"
        required
        placeholder="<?= L('WHAT_USER_ROLE') ?>"
        value="<?= $story->role ?>">
    </label>

    <label>
        <span class="label-title"><?= L('STORY_WHAT') ?></span>
        <textarea
        required
        placeholder="<?= L('WHAT_FUNCTIONALITIES') ?>"
        name="activity"><?= $story->activity ?></textarea>
    </label>

    <label>
        <span class="label-title"><?= L('STORY_FOR') ?></span>
        <textarea
        required
        placeholder="<?= L('ACHIEVE_WHAT_VALUE') ?>"
        name="value"><?= $story->value ?></textarea>
    </label>

    <label>
        <span class="label-title"><?= L('STORY_AC') ?></span>
        <div
        id="story-acceptances"
        class="editormd editormd-vertical">
            <textarea
            class="editormd-markdown-textarea"
            placeholder="<?= L('STORY_AC') ?>"
            name="acceptances"><?= $story->acceptances ?></textarea>
        </div>
    </label>

    <label>
        <span class="label-title"><?= L('OTHER_NOTES') ?></span>
        <div
        id="story-others"
        class="editormd editormd-vertical">
            <textarea
            class="editormd-markdown-textarea"
            placeholder="<?= L('OTHERS') ?>"
            name="extra"><?= $story->extra ?></textarea>
        </div>
    </label>

    <?php if ($editable) : ?>
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
        placeholder : "<?=
            L('OTHER_NOTES'),
            ' / ',
            L('ATTACHMENT'),
            L('ETC')
        ?>"
    },
    {
        id : 'story-acceptances',
        placeholder : "<?= L('STORY_AC') ?>"
    }
    ]
    $(function() {
        tryDisplayEditormd()
    })
</script>
<?php } ?>
