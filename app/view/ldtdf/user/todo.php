<?= $this->layout('main') ?>
<?= $this->title([L('TODO_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<table>
    <caption>
        <?= L('TODO_LIST') ?>
    </caption>

    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('TYPE') ?></th>
        <th><?= L('TITLE') ?></th>
        <th><?= L('PROJECT') ?></th>
        <th><?= L('STATUS') ?></th>
    </tr>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td><?= $task['id'] ?></td>
        <td class="text-task"><small><em><?= L('TASK') ?></em></small></td>
        <td>
            <a href="/dep/tasks/<?= $task->id ?>">
                <?= $task->story()->title, "(T{$task->id})" ?>
            </a>
        </td>
        <td><?= $task->project()->name ?></td>
        <td>
            <button class="btn-info">
                <?= L("STATUS_{$task->status}") ?>    
            </button>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>
