<header>
    <a href="<?= lrn() ?>">
        <button class="btn-logo"><em><?= ldtdf() ?></em></button>
    </a>

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
        <option value="<?= lrn('users/profile') ?>"><?= L('USER_PROFILE') ?></option>
        <option value="<?= lrn('users/logout') ?>"><?= L('LOGOUT') ?></option>
    </select>

    <span class="stub-3"></span>

    <?= $this->section('search') ?>
</header>
