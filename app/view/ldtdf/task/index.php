<?= $this->layout('main') ?>
<?= $this->title([L('TASK_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/tasks/new">
                <?= L('ADD_TASK') ?>
            </a>
        </button>
    </dd>
</dl>

<?= $this->section('filter/user', [
    'user' => 'CREATOR',
]) ?>

<table>
    <caption><?= L('TASK_LIST') ?></caption>

    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('STORY') ?></th>
        <th><?= L('PROJECT') ?></th>
        <th><?= L('CREATOR') ?></th>
        <th><?= L('TIME') ?></th>
        <th><?= L('STATUS') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>
    <?php if (isset($tasks) && iteratable($tasks)) : ?>
    <?php foreach ($tasks as $task) : ?>
    <tr>
        <td><?= $task->id ?></td>
        <td><?= $task->story()->title ?></td>
        <td><?= $task->project()->name ?></td>
        <td><?= $task->creator()->name ?></td>
        <td><?= $task->create_at ?></td>
        <td><?= L("STATUS_{$task->status}") ?></td>
        <td>
            <button>
                <a href="tasks/<?= $task->id ?>"><?= L('DETAILS') ?></a>
            </button>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
