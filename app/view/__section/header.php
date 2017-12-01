<header>
    <button class="btn-logo">
        <a href="/dep">
            <em><?= L('LDTDFMS') ?></em>
        </a>
    </button>

    <span class="stub"></span>

    <select name="loggedin">
        <option>
            <?=
                L('ROLE_'.share('user.role'))
                .': '
                .share('user.name')
                .' ('
                .share('user.account')
                .')'
            ?>
        </option>
        <option value="profile"><?= L('USER_PROFILE') ?></option>
        <option value="logout"><?= L('LOGOUT') ?></option>
    </select>

    <span class="stub-3"></span>

    <?= $this->section('search') ?>
</header>
