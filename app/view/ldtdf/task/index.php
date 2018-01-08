<?= $this->layout('main') ?>
<?= $this->title(ldtdf('TASK_LIST')) ?>
<?= $this->section('common') ?>

<?php if ($displaymenu ?? true): ?>
<dl class="list">
    <dd>
        <a href="<?= lrn('tasks/new') ?>">
            <button><?= L('ADD_TASK') ?></button>
        </a>
    </dd>
</dl>
<?php endif ?>

<table>
    <caption>
        <?= $this->section('search-by-id') ?>
        <?= L('TASK_LIST') ?>
    </caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $_GET['sort'] ?? 'desc' ?>"
        ><?= L('CREATE_TIME') ?></th>

        <th>
            <?= L('ORIGIN') ?>
            <?= $this->section('filter/common', [
                'name'   => 'origin',
                'list'   => [
                    'story' => L('STORY'),
                    'bug'   => L('BUG'),
                ],
            ]) ?>
        </th>
        <th><?= L('TITLE') ?></th>
        <th><?= L('PROJECT') ?>
            <?= $this->section('filter/common', [
                'name'   => 'project',
                'list'   => $projects,
            ]) ?>
        </th>
        <th>
            <?= L('CREATOR') ?>
            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
        </th>

        <?php if ($displayposition ?? true): ?>
        <?php $users[0] = '>> '.L('END').' <<'; ksort($users); ?>
        <th>
            <?= L('FLOW_POSITION') ?>
            <?= $this->section('filter/common', [
                'name'   => 'position',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
        </th>
        <?php endif ?>

        <th>
            <?= L('STATUS') ?>
            <?= $this->section('filter/common', [
                'name'   => 'status',
                'list'   => $status,
                'isUser' => false,
                'kval'   => true,
                'vlang'  => 'STATUS',
            ]) ?>
        </th>
    </tr>
    
    <?php if (isset($tasks) && iteratable($tasks)) : ?>
    <?php foreach ($tasks as $task) : ?>
    <tr>
        <td><?= $task->create_at ?></td>
        <td class="text-task"><small><em>
            <?= L($task->origin_type) ?>
        </em></small></td>
        <td>
            <sub><small><code>T<?= $task->id ?></code></small></sub>
            <a href="tasks/<?= $task->id ?>">
                <?= $task->origin('title') ?>
            </a>
        </td>
        <td><small><?= $task->project('name') ?></small></td>
        <td><small><?= $task->creator('name') ?></small></td>

        <?php if ($displayposition ?? true): ?>
        <td>
            <?php if ($name = $task->current('name')): ?>
            <small><?= $name ?></small>
            <?php else: ?>
            <button class="btn-info"><em><?= L('END') ?></em></button>
            <?php endif ?>
        </td>
        <?php endif ?>

        <td>
            <button class="btn-info">
                <?= L("STATUS_{$task->status}") ?>
            </button>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
