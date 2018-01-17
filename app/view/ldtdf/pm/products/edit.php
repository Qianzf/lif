<?= $this->layout('main') ?>

<?php if ($product ?? false): ?>
<?= $this->section('back2list', [
    'model'  => $product,
    'key'    => 'PRODUCT',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => lrn('pm/products'),
]) ?>

<?php $pid = $product->alive() ? $product->id : 'new'; ?>

<form method="POST" action='<?= lrn("pm/products/{$pid}") ?>'>
    <?= csrf_feild() ?>

    <label>
        <span class="label-title">* <?= L('PRODUCT_TITLE') ?></span>

        <input type="text" name="name" value="<?= $product->name ?>" required>
    </label>

    <label>
        <span class="label-title"><?= L('PRODUCT_DESCRIPTION') ?></span>

        <textarea name="desc"><?= $product->desc ?></textarea>
    </label>

    <label>
        <span class="label-title"><?= L('SORT') ?></span>

        <input type="number" name="order" value="<?= $product->order ?: 0 ?>">
    </label>
    
    <?= $this->section('submit', [
        'model' => $product
    ]) ?>
</form>
<?php endif ?>