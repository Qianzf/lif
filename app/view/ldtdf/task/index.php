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
    <dd>
        <button>
            <a href="/dep/bugs/new">
                <?= lang('ADD_BUG') ?>
            </a>
        </button>
    </dd>
</dl>

<table>
    <caption><?= lang('TASK_LIST') ?></caption>

    <tr>
        <th><?= lang('ID') ?></th>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($tasks) && $tasks) { ?>
    <?php foreach ($tasks as $task) { ?>
    <tr>
        <td><?= $task->id ?></td>
        <td><?= $task->title ?></td>
        <td>
            <button>
                <a href="tasks/<?= $task->id ?>"><?= lang('DETAILS') ?></a>
            </button>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
