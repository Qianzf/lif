<?= $this->layout('main') ?>
<?= $this->title([lang('TASK_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('search') ?>

<ul>
    <li>
        <a href="/dep/tasks/new">
            <?= lang('ADD_TASK') ?>
        </a>
    </li>
    <li>
        <a href="/dep/bugs/new">
            <?= lang('ADD_BUG') ?>
        </a>
    </li>
</ul>

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
            <a href="tasks/<?= $task->id ?>"><?= lang('DETAILS') ?></a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
