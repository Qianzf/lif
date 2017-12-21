<?= $this->layout('main') ?>
<?= $this->title([L('DOC_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="/dep/docs/new">
            <button><?= L('ADD_DOC') ?></button>
        </a>
    </dd>
    <dd>
        <a href="/dep/docs/folders/new">
            <button><?= L('ADD_FOLDER') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption><?= L('FOLDER'), '/', L('DOC_LIST') ?></caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $_GET['sort'] ?? 'desc' ?>"><?= L('CREATE_TIME') ?></th>
        <th><?= L('TITLE') ?></th>
        <th><?= L('TYPE') ?></th>
        <th>
            <?= L('CREATOR') ?>
            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
        </th>
    </tr>

    <?php if (isset($folders) && iteratable($folders)): ?>
    <?php foreach ($folders as $folder): ?>
    <tr>
        <td><?= $folder->create_at ?></td>
        <td>
            <a href="/dep/docs/folders/<?= $folder->id ?>">
                <?= $folder->title ?>
            </a>
        </td>
        <td><small><?= L('FOLDER') ?></small></td>
        <td><?= $folder->creator('name') ?></td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>

    <?php if (isset($docs) && iteratable($docs)): ?>
    <?php foreach ($docs as $doc): ?>
    <tr>
        <td><?= $doc->create_at ?></td>
        <td>
            <a href="/dep/docs/<?= $doc->id ?>">
                <?= $doc->title ?>
            </a>
        </td>
        <td><small><?= L('DOC') ?></small></td>
        <td><?= $doc->creator('name') ?></td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>