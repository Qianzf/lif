<?= $this->layout('main') ?>
<?= $this->title([L('USER_INFO'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>
<?= $this->section('title', [
    'key' => 'USER_INFO'
]) ?>

<div class="form">
    <label>
        <button class="btn-info">
            <?= L('NAME') ?>
        </button>
        <span class="stub"></span>
        <small><?= $user->name ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= L('ROLE') ?>
        </button>
        <span class="stub"></span>
        <small><?= L("ROLE_{$user->role}") ?></small>
    </label>

    <label>
        <button class="btn-info">
            <?= L('EMAIL') ?>
        </button>
        <span class="stub"></span>
        <a href="mailto:<?= $user->email ?>">
            <code><?= $user->email ?></code>
        </a>
    </label>    
</div>