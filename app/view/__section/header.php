<header>
    <strong><?= sysmsg('LDTDFMS') ?></strong>

    <select name="loggedin">
        <option><?= $nameWitchRole ?></option>
        <option value="profile"><?= sysmsg('USER_PROFILE') ?></option>
        <option value="logout"><?= sysmsg('USER_LOGOUT') ?></option>
    </select>
</header>
