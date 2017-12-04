<?= $this->layout('main') ?>
<?= $this->title([L('VIEW_BUG'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<h4>
    <?= L('VIEW_BUG') ?>

    <span class="stub"></span>
    <small><code>
        B<?= $bug->id ?>
    </code></small>

    <em><?= $bug->title ?></em>

    <?php if ($editable ?? false): ?>
    <button>
        <a href="/dep/bugs/<?= $bug->id ?>/edit"><?= L('EDIT') ?></a>
    </button>
    <?php endif ?>

    <?php if ($assignable ?? false): ?>
        <button>
            <a href="/dep/tasks/new?bug=<?= $bug->id ?>">
                <?= L('DISPATCH_TASK') ?>
            </a>
        </button>
    <?php endif ?>
</h4>

<?= $this->section('bug') ?>
<?= $this->section('trendings-with-sort', [
    'model'  => $bug,
    'object' => 'BUG',
    'displayRefType' => false,
]) ?>