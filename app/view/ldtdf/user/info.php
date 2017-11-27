<?= $this->layout('main') ?>
<?= $this->title([lang('USER_INFO'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>
<?= $this->section('title', [
    'key' => 'USER_INFO'
]) ?>

<div class="form">
    <label>
        <button class="btn-info">
            <?= lang('NAME') ?>
        </button>
        <span class="stub"></span>
        <small><?= $user->name ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= lang('ROLE') ?>
        </button>
        <span class="stub"></span>
        <small><?= lang("ROLE_{$user->role}") ?></small>
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
