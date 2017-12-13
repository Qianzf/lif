<?= $this->layout('main') ?>
<?= $this->title([L('BUG_LIST'), L('LDTDFMS')]) ?>

<?= $this->section('back2list', [
    'model'  => $doc,
    'key'    => 'DOC',
    'route'  => '/dep/docs',
]) ?>

<form method="POST">
    <?= csrf_feild() ?>

    <label>
        <span class="label-title"><?= L('TITLE') ?></span>
        <input
        value="<?= $doc->title ?>"
        placeholder="<?= L('DOC_TITLE') ?>"
        type="text"
        required="required"
        name="title">
    </label>

    <label>
        <span class="label-title"><?= L('FOLDER') ?></span>
        <select name="folder">
            <option value="0">-- <?= L('SELECT_FOLDER') ?> --</option>

            <?php if (isset($folders) && iteratable($folders)): ?>
            <?php foreach ($folders as $_folder): ?>
            <option
            <?php if ($folder && ($folder == $_folder->id)): ?>
            selected
            <?php elseif ($doc->folder == $_folder->id) : ?>
            selected
            <?php endif ?>
            value="<?= $_folder->id ?>">
                <?= "{$_folder->title} ({$_folder->id})" ?>
            </option>
            <?php endforeach ?>
            <?php endif ?>
        </select>
    </label>

    <label>
        <div
        id="doc-contents"
        class="editormd editormd-vertical">
            <textarea
            class="editormd-markdown-textarea"
            name="content"><?= $doc->content ?></textarea>
        </div>
    </label>

    <label>
        <span class="label-title"><?= L('SORT') ?></span>
        <input type="number" name="order" min="0" value="<?= $doc->order ?>">
    </label>

    <?= $this->section('submit', [
        'model' => $doc,
    ]) ?>
</form>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    var EditorMDObjects = [
    {
        id : 'doc-contents',
        placeholder : "<?= L('DOC_DETAILS') ?>"
    }
    ]
    $(function() {
        tryDisplayEditormd()
    })
</script>