<?= $this->layout('main') ?>
<?= $this->title([L('MY_UPLOAD'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dd>
    <dl>
        <button>
            <a href="/dep/tool/uploads/new" target="_blank">
                <?= L('ADD_UPLOAD') ?>
            </a>
        </button>
    </dl>
</dd>

<table>
    <caption><?= L('MY_UPLOAD') ?></caption>

    <tr>
        <th><?= L('TIME') ?></th>
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
            <button>
                <a href="/dep/tool/uploads/<?= $upload->id ?>">
                    <?= L('DETAILS') ?>
                </a>
            </button>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>