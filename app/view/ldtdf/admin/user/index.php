<?= $this->layout('main') ?>
<?= $this->title(ldtdf('USER_MANAGE')) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <a href="users/new">
            <button><?= L('ADD_USER') ?></button>
        </a>
    </dd>
    <dd>
        <a href="users/groups">
            <button><?= L('GROUP_MANAGE') ?></button>
        </a>
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
        <th>
            <?= L('STATUS') ?>
            <?= $this->section('filter/common', [
                'name' => 'status',
                'list' => [
                    0 => L('DISABLE'),
                    1 => L('NORMAL'),
                ],
            ]) ?>
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
            <small class="status-<?= $user->status ?>">
                <?= L($user->status ? 'NORMAL' : 'DISABLE') ?>
            </small>
        </td>
        <td>
            <a href="users/<?= $user->id ?>">
                <button><?= L('EDIT') ?></button>
            </a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
