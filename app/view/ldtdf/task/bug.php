<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_ORIGIN') ?></small>
    <span class="text-info">]</span>
    <small class="text-task"><i>
        <?= L($task->origin_type) ?>
        <code>B<?= $bug->id ?></code>
        <a href='<?= lrn("bugs/{$bug->id}") ?>'>
            <?= $bug->title ?>
        </a>
    </i></small>
</p>

<?= $this->section('bug') ?>
