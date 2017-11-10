<?= $this->layout('main') ?>
<?= $this->title([lang('USER_INFO'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<h4>
    <?= lang('USER_INFO') ?>
</h4>

<div class="form">
    <label>
        <button class="btn-info">
            <?= lang('NAME') ?>
        </button>
        <span class="stub"></span>
        <code><?= $user->name ?></code>
    </label>

    <label>
        <button class="btn-info">
            <?= lang('EMAIL') ?>
        </button>
        <span class="stub"></span>
        <a href="mailto:<?= $user->email ?>">
            <code><?= $user->email ?></code>
        </a>
    </label>    
</div>

<div class="vertical"></div>
