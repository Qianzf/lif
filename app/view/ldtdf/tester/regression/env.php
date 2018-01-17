<?= $this->layout('main') ?>
<?= $this->title(ldtdf('WAITTING_REGRESSION_LIST')) ?>
<?= $this->section('back2list', [
    'model' => $env,
    'key'   => 'REGRESSION_TEST_ENV_TASK',
    'action' => 'VIEW',
    'route'  => lrn('test/regressions'),
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
        <td><?= $task->project('name', false) ?: '-' ?></td>
        <td>
            <a href='<?= lrn("tasks/{$task->id}") ?>'>
                <?= $task->title() ?>
            </a>
        </td>
        <td>
            <a href='<?= lrn("test/regressions/env/{$env->id}/unpass/{$task->id}") ?>'>
                <button class="btn-delete"><?= L('SET_UNPASS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>