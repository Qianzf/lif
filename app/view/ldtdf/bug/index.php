<?= $this->layout('main') ?>
<?= $this->title(ldtdf('BUG_LIST')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="/dep/bugs/new">
            <button><?= L('ADD_BUG') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption><?= L('BUG_LIST') ?></caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $_GET['sort'] ?? 'desc' ?>"><?= L('CREATE_TIME') ?></th>
        <th><?= L('TITLE') ?></th>
        <th>
            <?= L('CREATOR') ?>
            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
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
    </tr>

    <?php if (isset($bugs) && iteratable($bugs)) : ?>
    <?php foreach ($bugs as $bug) : ?>
    <tr>
        <td><?= $bug->create_at ?></td>
        <td>
            <sub><small><code>B<?= $bug->id ?></code></small></sub>
            <a href="/dep/bugs/<?= $bug->id ?>">
                <?= $bug->title ?>
            </a>
        </td>
        <td><?= $bug->creator('name') ?></td>
        <td><?= $bug->os ?></td>
        <td><?= $bug->platform ?></td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
