<?= $this->layout('main') ?>
<?= $this->title([lang('BUG_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/bugs/new">
                <?= lang('ADD_BUG') ?>
            </a>
        </button>
    </dd>
</dl>
