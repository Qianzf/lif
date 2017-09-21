<header>
    <a href="/dep">
        <strong><?= lang('LDTDFMS') ?></strong>
    </a>

    <select name="loggedin">
        <option><?= share('nameWitchRole') ?></option>
        <option value="profile"><?= lang('USER_PROFILE') ?></option>
        <option value="logout"><?= lang('USER_LOGOUT') ?></option>
    </select>
</header>
