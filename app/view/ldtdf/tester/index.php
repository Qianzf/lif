<?= $this->layout('main') ?>
<?= $this->title(ldtdf('HOMEPAGE')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="<?= lrn('test/regressions') ?>">
            <button><?= L('REGRESSION_TEST') ?></button>
        </a>
    </dd>
</dl>
