<?= $this->layout('main') ?>
<?= $this->title([L('DOC_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="/dep/docs/new">
            <button><?= L('ADD_DOC') ?></button>
        </a>
    </dd>
</dl>
