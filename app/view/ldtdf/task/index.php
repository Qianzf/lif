<?= $this->layout('main') ?>
<?= $this->title([lang('TASK_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/tasks/new">
                <?= lang('ADD_TASK') ?>
            </a>
        </button>
    </dd>
</dl>

<?= $this->section('filter/user', [
    'user' => 'CREATOR',
]) ?>

<table>
    <caption><?= lang('TASK_LIST') ?></caption>

    <tr>
        <th><?= lang('ID') ?></th>
        <th><?= lang('STORY') ?></th>
        <th><?= lang('PROJECT') ?></th>
        <th><?= lang('CREATOR') ?></th>
        <th><?= lang('TIME') ?></th>
        <th><?= lang('STATUS') ?></th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>
    <?php if (isset($tasks) && iteratable($tasks)) : ?>
    <?php foreach ($tasks as $task) : ?>
    <tr>
        <td><?= $task->id ?></td>
        <td>
            <a href="/dep/stories/<?= $task->story()->id?>">
                <?= $task->story()->title ?>
            </a>
        </td>
        <td>
            <a href="/dep/projects/<?= $task->project()->id ?>">
                <?= $task->project()->name ?>
            </a>
        </td>
        <td><?= $task->creator()->name ?></td>
        <td><?= $task->create_at ?></td>
        <td><?= lang("STATUS_{$task->status}") ?></td>
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
