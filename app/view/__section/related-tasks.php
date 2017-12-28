<div>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_TASK') ?></small>
    <span class="text-info">]</span>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <table>
    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('PROJECT') ?></th>
        <th><?= L('PROJECT_TYPE') ?></th>
        <th><?= L('ENV') ?></th>
        <th><?= L('BRANCH') ?></th>
        <th><?= L('STATUS') ?></th>
        <th><?= L('CREATOR') ?></th>
        <th><?= L('FLOW_POSITION') ?></th>
    </tr>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td>
            <a href="/dep/tasks/<?= $task->id ?>">T<?= $task->id ?></a>
        </td>
        <td><?= $task->project('name') ?></td>
        <td><?= $task->project('type') ?></td>
        <td><?= $task->environment([], 'host') ?? '-' ?></td>
        <td><?= empty_safe($task->branch) ? '-' : $task->branch ?></td>
        <td>
            <button class="btn-info">
                <?= L("STATUS_{$task->status}") ?>
            </button>
        </td>
        <td><small><?= $task->creator('name') ?></small></td>
        <td><small>
            <?php if ($name = $task->current('name')): ?>
            <?= $name ?>
            <?php else: ?>
            <button class="btn-info"><em><?= L('END') ?></em></button>
            <?php endif ?>
        </small></td>
    </tr>
    <?php endforeach ?>
    </table>
    <?php endif ?>

    <span class="vertical"></span>
</div>