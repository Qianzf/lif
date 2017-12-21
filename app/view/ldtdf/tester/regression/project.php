<?= $this->layout('main') ?>
<?= $this->title([L('WAITTING_REGRESSION_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('back2list', [
    'model' => $project,
    'key'   => 'REGRESSION_TEST_PROJECT_TASK',
    'action' => 'VIEW',
    'route' => '/dep/test/regressions',
]) ?>

<table>
    <caption><?= $project->name ?></caption>

    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('PROJECT_TYPE') ?></th>
        <th><?= L('TASK') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <?php foreach ($tasks as $key => $task): ?>
    <tr>
        <td><?= $key+1 ?></td>
        <td><?= $project->type ?></td>
        <td>
            <a href="/dep/tasks/<?= $task->id ?>">
                <?= $task->title() ?>
            </a>
        </td>
        <td>
            <a href="/dep/test/regressions/project/<?= $project->id ?>/unpass/<?= $task->id ?>">
                <button class="btn-delete"><?= L('SET_UNPASS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>