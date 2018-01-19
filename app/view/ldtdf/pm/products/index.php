<?= $this->layout('main') ?>
<?= $this->title(ldtdf('PRODUCT_LIST')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="<?= lrn('pm/products/new') ?>">
            <button><?= L('ADD_PRODUCT') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption>
        <?= $this->section('search-by-id') ?>
        <?= L('PRODUCT_LIST') ?>
    </caption>

    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('PRODUCT_TITLE') ?></th>
        <th><?= L('CREATOR') ?></th>
        
        <th
        class="time-sort"
        data-sort="<?= $sort = $_GET['sort'] ?? 'desc' ?>">
            <i class="sort-<?= $sort ?>"></i>
            <?= L('CREATE_TIME') ?>
        </th>

        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (($products ?? false) && iteratable($products)): ?>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?= $product->id ?></td>
        <td><?= $this->escape($product->name) ?></td>
        <td><?= $this->escape($product->creator('name')) ?></td>
        <td><?= $product->create_at ?></td>
        <td>
            <button>
                <a href="<?= lrn('pm/products/'.$product->id).'/edit' ?>">
                    <?= L('DETAILS') ?>
                </a>
            </button>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>