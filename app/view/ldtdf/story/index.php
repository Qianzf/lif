<?= $this->layout('main') ?>
<?= $this->title(ldtdf('STORY_LIST')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="<?= lrn('stories/new') ?>">
            <button><?= L('ADD_STORY') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption>
        <?= $this->section('search-by-id') ?>
        <?= L('STORY_LIST') ?>
    </caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $sort = $_GET['sort'] ?? 'desc' ?>">
            <i class="sort-<?= $sort ?>"></i>
            <?= L('CREATE_TIME') ?>
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
            <?= L('CREATOR') ?>
            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
        </th>
    </tr>

    <?php if (isset($stories) && iteratable($stories)): ?>
    <?php foreach ($stories as $story): ?>
    <tr>
        <td><?= $story->create_at ?></td>
        <td>
            <sub><small><code>S<?= $story->id ?></code></small></sub>
            <a href='<?= lrn("stories/{$story->id}") ?>'>
                <?= $this->escape($story->title) ?>
            </a>
        </td>
        <td><?= $this->escape($story->product('name')) ?: '-' ?></td>
        <td>
            <a href='<?= lrn("users/{$story->creator('id')}") ?>'>
                <?= $this->escape($story->creator('name')) ?>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>