<?= $this->layout('main') ?>
<?= $this->title(ldtdf("VIEW_{$taskOrigin->type}")) ?>
<?= $this->section('common') ?>

<h4>
    <?= L("VIEW_{$taskOrigin->type}") ?>

    <span class="stub"></span>
    <small><code>
        <?= $taskOrigin->_type, $taskOrigin->id ?>
    </code></small>

    <em><?= $taskOrigin->title ?></em>

    <?php if ($editable ?? false): ?>
    <a href="<?= $originEditRoute ?>">
        <button><?= L('EDIT') ?></button>
    </a>
    <?php endif ?>

    <?php if ($assignable ?? false): ?>
        <a href="<?= $taskAddRoute ?>">
            <button><?= L('DISPATCH_TASK') ?></button>
        </a>
    <?php endif ?>
</h4>

<?= $this->section($taskOrigin->type) ?>
<?= $this->section('related-tasks') ?>
<?= $this->section('trendings-with-sort', [
    'model'  => $taskOrigin,
    'object' => $taskOrigin->type,
    'displayRefType' => false,
]) ?>