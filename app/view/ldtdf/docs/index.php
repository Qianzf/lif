<?= $this->layout('main') ?>
<?= $this->title([lang('DOC_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/docs/new">
                <?= lang('ADD_DOC') ?>
            </a>
        </button>
    </dd>
</dl>
