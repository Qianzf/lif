<?= $this->layout('main') ?>
<?= $this->title([lang('TODO_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<table>
    <caption>
        <?= lang('TODO_LIST') ?>
    </caption>

    <tr>
        <th><?= lang('ID') ?></th>
        <th><?= lang('TYPE') ?></th>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('STATUS') ?></th>
    </tr>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td><?= $task['id'] ?></td>
        <td class="text-task"><small><em><?= lang('TASK') ?></em></small></td>
        <td>
            <a href="/dep/tasks/<?= $task->id ?>">
                <?= $task->story()->title ?>
            </a>
        </td>
        <td>
            <?= lang("STATUS_{$task->status}") ?>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>
