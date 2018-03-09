<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model'  => $story,
    'key'    => 'STORY',
    'route'  => lrn('stories'),
]) ?>

<?php if (isset($story) && is_object($story)) { ?>
<?php $sid = $story->alive() ? $story->id : 'new'; ?>

<form method="POST" action='<?= lrn("stories/{$sid}") ?>'>
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
        <span class="label-title">
            <?= L('RELATED_PRODUCT') ?>
            <sub><small>(<?= L('OPTIONAL') ?>)</small></sub>
        </span>
        <select name="product">
            <option value="0">-- <?= L('SELECT_RELATED_PRODUCT') ?> --</option>
            <?php if (isset($products) && iteratable($products)): ?>
            <?php foreach ($products as $product): ?>
            <option
            <?php if (($pid = ($product['id'] ?? 0)) == $story->product): ?>
            selected
            <?php endif ?>
            value="<?= $pid ?>">
                <?= $product['name'] ?: L('UNKNOWN') ?>
            </option>
            <?php endforeach ?>
            <?php endif ?>
        </select>
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
        
        <div>
            <span class="label-title"></span>
            
            <div class="inblock" id="story-ac-list">
                <?php if (isset($acceptances) && iteratable($acceptances)): ?>
                <?php foreach ($acceptances as $acceptance): ?>
                <div>
                    <textarea
                    name="acceptance[<?= $acceptance->id ?>]"><?= $acceptance->detail ?></textarea>
                    <button
                    type="button"
                    required
                    onclick="deleteAC(this)"
                    class="btn-delete"><?= L('DELETE') ?></button>
                </div>
                <?php endforeach ?>
                <?php else: ?>
                <div>
                    <textarea
                    required
                    placeholder="<?= L('AT_LEAST_ONE') ?>"
                    name="acceptance[]"></textarea>
                </div>
                <?php endif ?>
            </div>

            <button type="button" onclick="addAC()"><?= L('ADD') ?></button>
        </div>
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

    <label>
        <span class="label-title"><?= L('PRIORITY') ?></span>
        <select name="priority">
            <?php if (isset($priorities) && iteratable($priorities)): ?>
            <?php foreach ($priorities as $priority): ?>
            <option
            <?php if (ci_equal($priority, $story->priority)): ?>
            selected
            <?php endif ?>
            value="<?= $priority ?>">
                <?= L("PRIORITY_STORY_{$priority}") ?>
            </option>
            <?php endforeach ?>
            <?php endif ?>
        </select>
    </label>

    <?= $this->section('developers', [
        'principals' => ($principals ?? []),
    ]) ?>

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
        tryDisplayEditormd("<?= lrn('tool/uploads/uptoken?raw=true') ?>")
    })
    function addAC() {
        let html = `
        <div>
            <textarea required name="acceptance[]"></textarea>
            <button
            type="button"
            onclick="deleteAC(this)"
            class="btn-delete"><?= L('DELETE') ?></button>
        </div>
        `

        $('#story-ac-list').append(html)
    }
    function deleteAC(obj) {
        $(obj).parent().remove()
    }
</script>
<?php } ?>
