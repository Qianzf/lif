<?= $this->layout('main') ?>
<?= $this->title([lang('STORY_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/stories/new">
                <?= lang('ADD_STORY') ?>
            </a>
        </button>
    </dd>
</dl>
