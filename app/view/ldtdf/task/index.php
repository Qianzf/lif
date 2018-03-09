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
        <td colspan="9">
            <button class="btn-info"><?= L('QUERY_FILTER') ?></button>

            <?= $this->section('filter/common', [
                'name'   => 'priority',
                'list'   => $priorities,
                'vlang'  => 'PRIORITY',
                'kval'   => true,
                'isUser' => false,
            ]) ?>
                

            <?= $this->section('filter/common', [
                'name'   => 'status',
                'list'   => $status,
                'isUser' => false,
                'kval'   => true,
                'vlang'  => 'STATUS',
            ]) ?>

            <?= $this->section('filter/common', [
                'name'   => 'origin',
                'list'   => [
                    'story' => L('STORY'),
                    'bug'   => L('BUG'),
                ],
            ]) ?>

            <?php $projects[0] = '>> '.L('NULL').' <<'; ksort($projects); ?>
            <?= $this->section('filter/common', [
                'name'   => 'project',
                'list'   => $projects,
            ]) ?>

            <?php $products[0] = '>> '.L('NULL').' <<'; ksort($products); ?>
            <?= $this->section('filter/common', [
                'name'   => 'product',
                'list'   => $products,
            ]) ?>

            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
            ]) ?>

            <?php if ($displayposition ?? true): ?>
            <?= $this->section('filter/common', [
                'name'   => 'position',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
            <?php endif ?>
        </td>
    </tr>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $sort = $_GET['sort'] ?? 'desc' ?>">
            <i class="sort-<?= $sort ?>"></i>
            <?= L('CREATE_TIME') ?>
        </th>

        <th><?= L('PRIORITY') ?></th>
        <th><?= L('ORIGIN') ?></th>
        <th><?= L('TITLE') ?></th>
        <th><?= L('PROJECT') ?></th>
        <th><?= L('PRODUCT') ?></th>
        <th><?= L('CREATOR') ?></th>

        <?php if ($displayposition ?? true): ?>
        <?php $users[0] = '>> '.L('END').' <<'; ksort($users); ?>
        <th><?= L('FLOW_POSITION') ?></th>
        <?php endif ?>

        <th><?= L('STATUS') ?></th>
    </tr>
    
    <?php if (isset($tasks) && iteratable($tasks)) : ?>
    <?php foreach ($tasks as $task) : ?>
    <tr>
        <td><?= $task->create_at ?></td>
        <td><small>
            <?= L("PRIORITY_{$task->origin('priority')}") ?>
        </small></td>
        <td class="text-task"><small><em>
            <?= L($task->origin_type) ?>
        </em></small></td>
        <td>
            <sub><small><code>T<?= $task->id ?></code></small></sub>
            <a href="tasks/<?= $task->id ?>">
                <?= $task->origin('title') ?>
            </a>
        </td>
        <td><small><?= $task->project('name', false) ?: '-' ?></small></td>
        <td><small><?= $task->product('name') ?: '-' ?></small></td>
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
