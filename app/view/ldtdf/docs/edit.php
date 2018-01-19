<?= $this->layout('main') ?>
<?= $this->title(ldtdf('BUG_LIST')) ?>

<?= $this->section('back2list', [
    'model'  => $doc,
    'key'    => 'DOC',
    'route'  => lrn('docs'),
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

    <?= $this->section('treeselect', [
        'inputTitle' => L('FOLDER'),
        'inputName'  => 'folder',
        'inputDefaultValue'  => ($doc->alive() ? $doc->folder : $folder->id),
        'inputDefaultOutput' => ($doc->alive() ? $doc->folder('title') : $folder->title),
        'treeData' => $folders,
    ]) ?>

    <label>
        <span class="label-title"><?= L('SORT') ?></span>
        <input type="number" name="order" min="0" value="<?= $doc->order ?>">
    </label>

    <label>
        <span class="label-title"><?= L('DETAILS') ?></span>
        <div
        id="doc-contents"
        class="editormd editormd-vertical">
            <textarea
            class="editormd-markdown-textarea"
            name="content"><?= $doc->content ?></textarea>
        </div>
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
        placeholder : "<?= L('DOC_DETAILS') ?>",
        height : 400,
    }
    ]
    $(function() {
        tryDisplayEditormd("<?= lrn('tool/uploads/uptoken?raw=true') ?>")
    })
</script>