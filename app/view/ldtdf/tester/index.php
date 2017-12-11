<?= $this->layout('main') ?>
<?= $this->title([L('HOMEPAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="test/regressions">
            <button><?= L('REGRESSION_TEST') ?></button>
        </a>
    </dd>
</dl>
