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

    <?= $this->section('treeselect', [
        'inputTitle' => L('FOLDER'),
        'inputName'  => 'folder',
        'inputDefaultValue'  => ($doc->isAlive() ? $doc->folder : $folder->id),
        'inputDefaultOutput' => ($doc->isAlive() ? $doc->folder('title') : $folder->title),
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
        placeholder : "<?= L('DOC_DETAILS') ?>"
    }
    ]
    $(function() {
        tryDisplayEditormd()
    })
</script>