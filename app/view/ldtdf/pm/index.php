<?= $this->layout('main') ?>
<?= $this->title(ldtdf('HOMEPAGE')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="<?= lrn('pm/products') ?>">
            <button><?= L('PRODUCT_MANAGE') ?></button>
        </a>
    </dd>
</dl>