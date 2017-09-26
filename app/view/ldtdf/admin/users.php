<?= $this->layout('main') ?>
<?= $this->title([lang('USER_MANAGE'), lang('LDTDFMS')]) ?>

<ul>
    <li>
        <input type="text" name="search"
        value="<?= $keyword ?>" required
        placeholder="<?= lang('PROVIDE_KEYWORDS') ?>">

        <input type="button" name="search-btn"
        value="<?= lang('SEARCH') ?>">

        <input type="button" name="clear-search-btn"
        value="<?= lang('CLEAR') ?>">

        <input type="button" name="reset-all-btn"
        value="<?= lang('RESET_ALL') ?>">
    </li>

    <br>

    <li>
        <a href="users/new"><?= lang('ADD_USER') ?></a>
    </li>
</ul>

<table border="1">
    <caption><?= lang('USER_LIST') ?></caption>
    <tr>
        <th><?= lang('ACCOUNT') ?></th>
        <th><?= lang('NAME') ?></th>
        <th><?= lang('EMAIL') ?></th>
        <th>
            <?= lang('USER_ROLE') ?>
            <select name="system-roles">
                <option value="all"><?= lang('ALL') ?></option>
                <?php foreach (share('system-roles') as $role) { ?>
                <?php $selected = ($searchrole == $role) ? 'selected' : '' ?>
                <option value="<?= $role ?>" <?= $selected ?>>
                    <?= lang($role) ?>
                </option>
                <?php } ?>
            </select>
        </th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>

    <?php if ($users) { ?>
    <?php foreach($users as $user) { ?>
    <tr <?php echo $keyword ? 'class="search-res"' : ''; ?>>
        <td><?= $user->account ?></td>
        <td><?= $user->name ?></td>
        <td><?= $user->email ?></td>
        <td><?= lang($user->role) ?></td>
        <td>
            <a href="users/edit/<?= $user->id ?>"><?= lang('EDIT') ?></a>
            <a href="users/delete/<?= $user->id ?>"><?= lang('DELETE') ?></a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

