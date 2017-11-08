<?= $this->layout('main') ?>
<?= $this->title([lang('USER_MANAGE'), lang('LDTDFMS')]) ?>

<ul>
    <li>
        <a href="users/new"><?= lang('ADD_USER') ?></a>
    </li>
</ul>

<?= $this->section('search') ?>

<table>
    <caption><?= lang('USER_LIST') ?></caption>
    <tr>
        <th><?= lang('ACCOUNT') ?></th>
        <th><?= lang('NAME') ?></th>
        <th><?= lang('EMAIL') ?></th>
        <th>
            <?= lang('USER_ROLE') ?>
            <?= $this->section('filter/roles') ?>
        </th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($users) && $users) { ?>
    <?php foreach($users as $user) { ?>
    <tr <?php echo $keyword ? 'class="search-res"' : ''; ?>>
        <td><?= $user->account ?></td>
        <td><?= $user->name ?></td>
        <td><?= $user->email ?></td>
        <td><?= lang($user->role) ?></td>
        <td>
            <a href="users/<?= $user->id ?>"><?= lang('EDIT') ?></a>
            <a href="users/delete/<?= $user->id ?>"><?= lang('DELETE') ?></a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
