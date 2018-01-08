<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_ORIGIN') ?></small>
    <span class="text-info">]</span>
    <small class="text-task"><i>
        <?= L($task->origin_type) ?>
        <code>S<?= $story->id ?></code>
        <a href='<?= lrn("stories/{$story->id}") ?>'>
            <?= $story->title ?>
        </a>
    </i></small>
</p>

<?= $this->section('story') ?>
