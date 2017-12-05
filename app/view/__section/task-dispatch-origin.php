<?php $title = L("VIEW_{$taskOrigin->type}"); ?>

<?= $this->layout('main') ?>
<?= $this->title([$title, L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<h4>
    <?= $title ?>

    <span class="stub"></span>
    <small><code>
        <?= $taskOrigin->_type, $taskOrigin->id ?>
    </code></small>

    <em><?= $taskOrigin->title ?></em>

    <?php if ($editable ?? false): ?>
    <button>
        <a href="<?= $originEditRoute ?>"><?= L('EDIT') ?></a>
    </button>
    <?php endif ?>

    <?php if ($assignable ?? false): ?>
        <button>
            <a href="<?= $taskAddRoute ?>">
                <?= L('DISPATCH_TASK') ?>
            </a>
        </button>
    <?php endif ?>
</h4>

<?= $this->section($taskOrigin->type) ?>
<?= $this->section('related-tasks') ?>
<?= $this->section('trendings-with-sort', [
    'model'  => $taskOrigin,
    'object' => $taskOrigin->type,
    'displayRefType' => false,
]) ?>