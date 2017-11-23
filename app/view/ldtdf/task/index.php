<?= $this->layout('main') ?>
<?= $this->title([lang('TASK_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>
<?= $this->section('filter/user') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/tasks/new">
                <?= lang('ADD_TASK') ?>
            </a>
        </button>
    </dd>
</dl>

<table>
    <caption><?= lang('TASK_LIST') ?></caption>

    <tr>
        <th><?= lang('ID') ?></th>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('CREATOR') ?></th>
        <th><?= lang('STATUS') ?></th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($tasks) && $tasks) : ?>
    <?php foreach ($tasks as $task) : ?>
    <tr>
        <td><?= $task->id ?></td>
        <td><?= $task->title ?></td>
        <td><?= $task->creator()->name ?></td>
        <td><?= lang("TASK_{$task->status}") ?></td>
        <td>
            <button>
                <a href="tasks/<?= $task->id ?>"><?= lang('DETAILS') ?></a>
            </button>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
