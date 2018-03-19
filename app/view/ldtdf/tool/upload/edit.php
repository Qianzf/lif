<?= $this->layout('main') ?>
<?= $this->title(ldtdf('EDIT_UPLOAD_FILE')) ?>
<?= $this->section('back2list', [
    'model' => $upload,
    'key' => 'UPLOAD_FILE',
    'route' => lrn('tool/uploads'),
]) ?>

<form method="POST">
    <?= csrf_field() ?>
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