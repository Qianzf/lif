<?= $this->layout('main') ?>
<?= $this->title([L('FOLDER_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('back2list', [
    'model'   => $folder,
    'key'     => 'FOLDER',
    'route'   => '/dep/docs',
    'buttons' => [
        [
            'name'  => 'CREATE_DOC',
            'route' => "/dep/docs/new?folder={$folder->id}",
        ],
        [
            'name'  => 'CREATE_FOLDER',
            'route' => "/dep/docs/folders/new?parent={$folder->id}",
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
        'inputDefaultValue' => $folder->parent,
        'inputDefaultOutput' => $folder->parent('title'),
        'treeData' => ($folders ?? []),
    ]) ?>

    <label>
        <span class="label-title"><?= L('SORT') ?></span>
        <input type="number" name="order" min="0" value="<?= $folder->order ?>">
    </label>

    <?= $this->section('submit', [
        'model' => $folder,
    ])  ?>
</form>