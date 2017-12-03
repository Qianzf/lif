<?= $this->layout('main') ?>
<?= $this->title([L('FILE_UPLOAD'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>
<h4>
    <?= L('UPLOAD_FILE') ?>
    <sup><small>
        <a href="/dep/tool/uploads"><?= L('BACK_TO_LIST') ?></a>
    </small></sup>
</h4>
<?= $this->section('qnupload') ?>