<?= $this->layout('main') ?>
<?= $this->title([lang('GROUP_MANAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <button>
            <a href="/dep/admin/users/groups/new"><?= lang('ADD_GROUP') ?></a>
        </button>
    </dd>
</dl>

<table>
    <caption>
        <?= lang('GROUP_LIST') ?>
    </caption>

    <tr>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($groups) && iteratable($groups)) : ?>
    <?php foreach ($groups as $group): ?>
        <tr>
            <td><?= $group->name ?></td>
            <td>
                <button>
                    <a href="/dep/admin/users/groups/<?= $group->id ?>">
                        <?= lang('EDIT') ?>
                    </a>
                </button>
            </td>
        </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
