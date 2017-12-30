<?= $this->layout('main') ?>
<?= $this->title(ldtdf('VIEW_DOC_FOLDER')) ?>
<?= $this->section('common') ?>

<h2>
    <sub><code><?= "F{$folder->id}" ?></code></sub>
    <big><?= $folder->title ?></big>

    <?= $this->section('back_to', [
        'route' => '/dep/docs',
    ]) ?>

    <a href="/dep/docs/folders/<?= $folder->id ?>/edit">
        <button><?= L('EDIT') ?></button>
    </a>

    <a href="/dep/docs/new?folder=<?= $folder->id ?>">
        <button><?= L('ADD_DOC') ?></button>
    </a>

    <a href="/dep/docs/folders/new?parent=<?= $folder->id ?>">
        <button><?= L('ADD_CATE') ?></button>
    </a>
</h2>

<div class="doc-show-container">
    <div class="doc-show-folder">
        <?= $this->section('doc-treeview') ?>
    </div>

    <div class="doc-show-content">
        <?php if ($doc->alive()): ?>
        <?= $this->section('doc', [
            'display' => false,
        ]) ?>
        <?php endif ?>
    </div>
</div>