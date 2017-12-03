<?= $this->layout('main') ?>
<?= $this->title([L('EDIT_UPLOAD_FILE'), L('LDTDFMS')]) ?>
<?= $this->section('back2list', [
    'model' => $upload,
    'key' => 'UPLOAD_FILE',
    'route' => '/dep/tool/uploads',
]) ?>

<form method="POST">
    <?= csrf_feild() ?>
    <input
    type="hidden"
    name="filekey"
    value="<?= $upload->filekey ?>">
    <label>
        <span class="label-title"><?= L('FILE_URL') ?></span>
        <input type="text" disabled value="<?= $fileurl ?>">
    </label>
    <label>
        <span class="label-title"><?= L('FILE_TITLE') ?></span>
        <input type="text" name="filename" value="<?= $upload->filename ?>">
    </label>
    <label>
        <span class="label-title"></span>
        <input
        value="<?= L('UPDATE_UPLOAD_FILE') ?>"
        type="submit">
    </label>
</form>