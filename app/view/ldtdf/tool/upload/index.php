<?= $this->layout('main') ?>
<?= $this->title([L('MY_UPLOAD'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dd>
    <dl>
        <a href="/dep/tool/uploads/new" target="_blank">
            <button><?= L('ADD_UPLOAD') ?></button>
        </a>
    </dl>
</dd>

<table>
    <caption><?= L('MY_UPLOAD') ?></caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $_GET['sort'] ?? 'desc' ?>"><?= L('UPLOAD_TIME') ?></th>
        <th><?= L('FILENAME') ?></th>
        <th><?= L('TITLE') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($uploads) && iteratable($uploads)): ?>
    <?php foreach ($uploads as $upload): ?>
    <tr>
        <td><?= $upload->create_at ?></td>
        <td><?= $upload->filekey ?></td>
        <td><?= $upload->filename ?></td>
        <td>
            <a href="/dep/tool/uploads/<?= $upload->id ?>">
                <button><?= L('DETAILS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>