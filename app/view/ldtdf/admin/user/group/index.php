<?= $this->layout('main') ?>
<?= $this->title([L('GROUP_MANAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <button>
            <a href="/dep/admin/users/groups/new"><?= L('ADD_GROUP') ?></a>
        </button>
    </dd>
</dl>

<table>
    <caption>
        <?= L('GROUP_LIST') ?>
    </caption>

    <tr>
        <th><?= L('TITLE') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($groups) && iteratable($groups)) : ?>
    <?php foreach ($groups as $group): ?>
        <tr>
            <td><?= $group->name ?></td>
            <td>
                <button>
                    <a href="/dep/admin/users/groups/<?= $group->id ?>">
                        <?= L('EDIT') ?>
                    </a>
                </button>
            </td>
        </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
