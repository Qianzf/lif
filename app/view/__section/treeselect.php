<?= $this->css([
    // 'treeselect/dist/bootstrap',
    'treeselect/lib/ztree/zTreeStyle',
    'treeselect/dist/treeSelect',
]) ?>

<?= $this->js([
    'treeselect/lib/ztree/jquery.ztree.core-3.5',
    'treeselect/dist/treeSelect',
]) ?>

<label for="tree-select">
    <span class="label-title"><?= $inputTitle ?? L('SELECT_CATE') ?></span>
    <input
    type="hidden"
    name="<?= $inputName ?? null ?>"
    value="<?= $inputDefaultValue ?? '' ?>">
    <div id="tree-select" class="inblock"></div>
</label>

<script>
// See ztree api docs: http://www.treejs.cn/v3/api.php
new TreeSelect({
    element: '#tree-select',
    data: <?= _json_encode($treeData ?? []) ?>,
    valueKey: 'id',
    inputName: '<?= $inputName ?? "tree-selected-leaf" ?>',
    inputDefaultValue: '<?= $inputName ?? "tree-selected-leaf" ?>',
    inputDefaultOutput: '<?= $inputDefaultOutput ?? '-- '.L('SELECT_CATE').' --' ?>',
    async: {
        enable: true,
        type: 'get',
        contentType: 'application/json',
        dataType: 'json',
        autoParam: ['id'],
        otherParam: {'dat-only': true},
        url: '/dep/docs/folders/children'
    }
})
</script>
