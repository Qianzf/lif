<?= $this->layout('main') ?>
<?= $this->title(ldtdf('FOLDER_LIST')) ?>
<?= $this->section('back2list', [
    'model'   => $folder,
    'key'     => 'FOLDER',
    'route'   => lrn('docs'),
    'buttons' => [
        [
            'name'  => 'CREATE_DOC',
            'route' => lrn("docs/new?folder={$folder->id}"),
            'alive' => 'true',
        ],
        [
            'name'  => 'CREATE_FOLDER',
            'route' => lrn("docs/folders/new?parent={$folder->id}"),
            'alive' => 'true',
        ],
    ],
]) ?>

<form method="POST">
    <?= csrf_feild() ?>

    <label>
        <span class="label-title"><?= L('TITLE') ?></span>
        <input
        value="<?= $folder->title ?>"
        placeholder="<?= L('DOC_FOLDER_TITLE') ?>"
        type="text"
        name="title"
        required="required">
    </label>

    <label>
        <span class="label-title"><?= L('DESCRIPTION') ?></span>
        <textarea type="text" name="desc"><?= $folder->desc ?></textarea>
    </label>

    <?= $this->section('treeselect', [
        'inputTitle' => L('PARENT_CATE'),
        'inputName'  => 'parent',
        'inputDefaultValue'  => intval($folder->alive() ? $folder->parent : $parent->id),
        'inputDefaultOutput' => ($folder->alive() ? $folder->parent('title') : $parent->title),
        'treeData' => ($folders ?? []),
    ]) ?>

    <label>
        <span class="label-title"><?= L('SORT') ?></span>
        <input
        type="number"
        name="order"
        min="0"
        value="<?= intval($folder->order) ?>">
    </label>

    <?= $this->section('submit', [
        'model' => $folder,
    ])  ?>
</form>