<header>
    <button class="btn-logo">
        <a href="/dep">
            <em><?= lang('LDTDFMS') ?></em>
        </a>
    </button>

    <span class="stub"></span>

    <select name="loggedin">
        <option>
            <?=
                lang('ROLE_'.share('user.role'))
                .': '
                .share('user.name')
                .' ('
                .share('user.account')
                .')'
            ?>
        </option>
        <option value="profile"><?= lang('USER_PROFILE') ?></option>
        <option value="logout"><?= lang('LOGOUT') ?></option>
    </select>

    <span class="stub-3"></span>

    <?= $this->section('search') ?>
</header>
