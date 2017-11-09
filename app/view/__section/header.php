<header>
    <a href="/dep">
        <em><?= lang('LDTDFMS') ?></em>
    </a>

    <select name="loggedin">
        <option>
            <?=
                lang(share('__USER.role'))
                .': '.
                share('__USER.account')
            ?>
        </option>
        <option value="profile"><?= lang('USER_PROFILE') ?></option>
        <option value="logout"><?= lang('LOGOUT') ?></option>
    </select>

    <?= $this->section('search') ?>
</header>
