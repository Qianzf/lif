<?= $this->layout('main') ?>
<?= $this->title([L('USER_MANAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <button>
            <a href="users/new"><?= L('ADD_USER') ?></a>
        </button>
    </dd>
    <dd>
        <button>
            <a href="users/groups"><?= L('GROUP_MANAGE') ?></a>
        </button>
    </dd>
</dl>

<table>
    <caption><?= L('USER_LIST') ?></caption>
    <tr>
        <th><?= L('ACCOUNT') ?></th>
        <th><?= L('NAME') ?></th>
        <th><?= L('EMAIL') ?></th>
        <th>
            <?= L('USER_ROLE') ?>
            <?= $this->section('filter/roles') ?>
        </th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($users) && $users) { ?>
    <?php foreach($users as $user) { ?>
    <tr <?php echo $keyword ? 'class="search-res"' : ''; ?>>
        <td><?= $user->account ?></td>
        <td><?= $user->name ?></td>
        <td><?= $user->email ?></td>
        <td><?= L("ROLE_{$user->role}") ?></td>
        <td>
            <button>
                <a href="users/<?= $user->id ?>">
                    <?= L('EDIT') ?>
                </a>
            </button>
            <button class="btn-delete">
                <a href="users/delete/<?= $user->id ?>">
                    <?= L('DELETE') ?>
                </a>
            </button>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
