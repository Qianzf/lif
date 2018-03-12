<?= $this->layout('main') ?>
<?= $this->title(ldtdf('HTTP API')) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <a href="<?= lrn('tool/httpapi/projects/new') ?>">
            <button><?= L('ADD_PROJECT') ?></button>
        </a>
    </dd>
</dl>


<table>
    <caption><?= L('PROJECT_LIST') ?></caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $sort = $_GET['sort'] ?? 'desc' ?>">
            <i class="sort-<?= $sort ?>"></i>
            <?= L('CREATE_TIME') ?>
        </th>

        <th><?= L('TITLE') ?></th>

        <th>
            <?= L('CREATOR') ?>
            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
        </th>

        <th>
            <?= L('OPERATIONS') ?>
        </th>
    </tr>

    <?php if (isset($projects) && iteratable($projects)): ?>
    <?php foreach ($projects as $project): ?>
    <tr>
        <td><?= $project->create_at ?></td>
        <td><?= $project->name ?></td>
        <td><?= $project->creator('name') ?></td>

        <td>
            <a href="<?= lrn('tool/httpapi/projects/'.$project->id) ?>">
                <button class="btn-info"><?= L('DETAILS') ?></button>
            </a>
            <a href='<?= lrn("tool/httpapi/projects/{$project->id}/edit") ?>'>
                <button class="btn-edit"><?= L('EDIT') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>
