<?= $this->layout('main') ?>
<?= $this->title(ldtdf('GROUP_MANAGE')) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <a href="/dep/admin/users/groups/new">
            <button><?= L('ADD_GROUP') ?></button>
        </a>
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
                <a href="/dep/admin/users/groups/<?= $group->id ?>">
                    <button><?= L('EDIT') ?></button>
                </a>
            </td>
        </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
