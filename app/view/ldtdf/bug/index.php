<?= $this->layout('main') ?>
<?= $this->title([L('BUG_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/bugs/new">
                <?= L('ADD_BUG') ?>
            </a>
        </button>
    </dd>
</dl>
