<?= $this->layout('main') ?>
<?= $this->title(ldtdf('TOOL')) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <a href="<?= lrn('tool/uploads') ?>">
            <button><?= L('FILE_UPLOAD') ?></button>
        </a>
    </dd>

    <dd>
        <a href="<?= lrn('tool/httpapi') ?>">
            <button>HTTP API</button>
        </a>
    </dd>
</dl>
