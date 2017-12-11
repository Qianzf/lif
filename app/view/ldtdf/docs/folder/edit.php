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

    <label>
        <span class="label-title"><?= L('PARENT_CATE') ?></span>
        <select name="parent">
            <option value="0"><?= L('NULL') ?></option>
            <?php if (isset($folders) && iteratable($folders)): ?>
            <?php foreach ($folders as $_folder): ?>
            <option
            <?php if ($_folder->id == $folder->parent): ?>
            selected
            <?php elseif ($parent == $_folder->id): ?>
            selected
            <?php endif ?>
            value="<?= $_folder->id ?>">
                <?="{$_folder->title} ({$_folder->id})" ?>
            </option>
            <?php endforeach ?>
            <?php endif ?>
        </select>
    </label>

    <?= $this->section('submit', [
        'model' => $folder,
    ])  ?>
</form>