<?= $this->layout('main') ?>
<?= $this->title(ldtdf('VIEW_DOC_FOLDER')) ?>
<?= $this->section('common') ?>

<?php $folderId = $folder->id ?? 0; ?>

<h2>
    <sub><code><?= "F{$folderId}" ?></code></sub>
    <big><?= $folder->title ?></big>

    <?= $this->section('back_to', [
        'route' => lrn('docs'),
    ]) ?>

    <a href='<?= lrn("docs/folders/{$folderId}/edit") ?>'>
        <button><?= L('EDIT') ?></button>
    </a>

    <a href='<?= lrn("docs/new?folder={$folderId}&parent={$folderId}") ?>'>
        <button><?= L('ADD_DOC') ?></button>
    </a>

    <a href='<?=
        lrn("docs/folders/new?parent={$folderId}&folder={$folderId}")
    ?>'>
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