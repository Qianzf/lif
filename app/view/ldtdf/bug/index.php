<?= $this->layout('main') ?>
<?= $this->title(ldtdf('BUG_LIST')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="<?= lrn('bugs/new') ?>">
            <button><?= L('ADD_BUG') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption>
        <?= $this->section('search-by-id') ?>
        <?= L('BUG_LIST') ?>
    </caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $sort = $_GET['sort'] ?? 'desc' ?>">
            <i class="sort-<?= $sort ?>"></i>
            <?= L('CREATE_TIME') ?>
        </th>
        <th>
            <?= L('PRIORITY') ?>
            <?= $this->section('filter/common', [
                'name'   => 'priority',
                'list'   => $priorities,
                'vlang'  => 'PRIORITY_BUG',
                'kval'   => true,
                'isUser' => false,
            ]) ?>
        </th>
        <th><?= L('TITLE') ?></th>
        <th>
            <?= L('RELATED_PRODUCT') ?>
            <?php $products[0] = '>> '.L('NULL').' <<'; ?>
            <?= $this->section('filter/common', [
                'name'   => 'product',
                'list'   => $products,
            ]) ?>
        </th>
        <th>
            <?= L('OS') ?>
            <?= $this->section('filter/common', [
                'name'   => 'os',
                'list'   => $oses,
                'kval'   => true,
                'isUser' => false,
            ]) ?>
        </th>
        <th><?= L('PLATFORM') ?></th>
        <th>
            <?= L('CREATOR') ?>
            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
        </th>
    </tr>

    <?php if (isset($bugs) && iteratable($bugs)) : ?>
    <?php foreach ($bugs as $bug) : ?>
    <tr>
        <td><?= $bug->create_at ?></td>
        <td>
            <small class="priority-<?= $bug->priority ?>">
                <?= L("PRIORITY_BUG_{$bug->priority}") ?>
            </small>
        </td>
        <td>
            <sub><small><code>B<?= $bug->id ?></code></small></sub>
            <a href='<?= lrn("bugs/{$bug->id}") ?>'>
                <?= $this->escape($bug->title) ?>
            </a>
        </td>
        <td><?= $this->escape($bug->product('name')) ?: '-' ?></td>
        <td><?= $this->escape($bug->os) ?></td>
        <td><?= $this->escape($bug->platform) ?></td>
        <td><?= $this->escape($bug->creator('name')) ?></td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
