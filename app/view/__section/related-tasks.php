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
                <?= $task->project('name') ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

    <span class="vertical"></span>
</div>