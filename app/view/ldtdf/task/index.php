<?= $this->layout('main') ?>
<?= $this->title(ldtdf('TASK_LIST')) ?>
<?= $this->section('common') ?>

<?php if ($displaymenu ?? true): ?>
<dl class="list">
    <dd>
        <a href="/dep/tasks/new">
            <button><?= L('ADD_TASK') ?></button>
        </a>
    </dd>
</dl>
<?php endif ?>

<table>
    <caption><?= L('TASK_LIST') ?></caption>

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
        <td><?= $task->project('name') ?></td>
        <td><?= $task->creator('name') ?></td>

        <?php if ($displayposition ?? true): ?>
        <td><?= $task->current('name') ?></td>
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
