<?= $this->layout('main') ?>
<?= $this->title([L('WAITTING_REGRESSION_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('back2list', [
    'model' => $env,
    'key'   => 'REGRESSION_TEST_ENV_TASK',
    'action' => 'VIEW',
    'route' => '/dep/test/regressions',
]) ?>

<table>
    <caption><?= $env->host ?></caption>

    <tr>
        <th><?= L('PROJECT') ?></th>
        <th><?= L('TASK') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td><?= $task->project('name') ?></td>
        <td><?= $task->title() ?></td>
        <td>
            <a href="/dep/tasks/<?= $task->id ?>">
                <button><?= L('DETAILS') ?></button>
            </a>
            <a href="/dep/test/regressions/<?= $env->id ?>/<?= $task->id ?>/unpass">
                <button class="btn-delete"><?= L('SET_UNPASS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>