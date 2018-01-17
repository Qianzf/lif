<?= $this->layout('main') ?>
<?= $this->title(ldtdf('PRODUCT_INFO')) ?>
<?= $this->section('common') ?>
<?= $this->section('title', [
    'key' => 'PRODUCT_INFO'
]) ?>

<div class="form">
    <label>
        <button class="btn-info">
            <?= L('TITLE') ?>
        </button>
        <span class="stub"></span>
        <small><?= $this->escape($product->name) ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= L('DESCRIPTION') ?>
        </button>
        <span class="stub"></span>
        <small><?= $this->escape($product->desc) ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= L('CREATOR') ?>
        </button>
        <span class="stub"></span>
        <small>
            <?= $this->escape($product->creator('name') ?: L('UNKNOWN')) ?>
        </small>
    </label>

    <label>
        <button class="btn-info">
            <?= L('CREATE_TIME') ?>
        </button>
        <span class="stub"></span>
        <small>
            <?= $product->create_at ?>
        </small>
    </label>
</div>

<div class="vertical"></div>
