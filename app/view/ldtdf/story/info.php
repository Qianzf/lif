<?= $this->layout('main') ?>
<?= $this->title([L('VIEW_STORY'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<h4>
    <?= L('VIEW_STORY') ?>

    <span class="stub"></span>
    <small><code>
        S<?= $story->id ?>
    </code></small>

    <em><?= $story->title ?></em>

    <?php if ($editable ?? false): ?>
    <button>
        <a href="/dep/stories/<?= $story->id ?>/edit"><?= L('EDIT') ?></a>
    </button>
    <?php endif ?>

    <?php if ($assignable ?? false): ?>
        <button>
            <a href="/dep/tasks/new?story=<?= $story->id ?>">
                <?= L('DISPATCH_TASK') ?>
            </a>
        </button>
    <?php endif ?>
</h4>

<?= $this->section('story') ?>

<div>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_TASK') ?></small>
    <span class="text-info">]</span>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <ul>
        <?php foreach ($tasks as $task): ?>
        <li>
            <a href="/dep/tasks/<?= $task->id ?>">
                T<?= $task->id ?>:
                <?= $task->project()->name ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

    <span class="vertical"></span>
</div>

<?= $this->section('trendings-with-sort', [
    'model'  => $story,
    'object' => 'STORY',
    'displayRefType' => false,
]) ?>