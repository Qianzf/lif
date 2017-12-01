<?= $this->layout('main') ?>
<?= $this->title([L('DOC_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/docs/new">
                <?= L('ADD_DOC') ?>
            </a>
        </button>
    </dd>
</dl>
