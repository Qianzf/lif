<?= $this->layout('main') ?>
<?= $this->title(ldtdf('PROJECT_MANAGE')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="/dep/admin/projects/new">
            <button><?= L('ADD_PROJECT') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption>
        <?= L('PROJECT_LIST') ?>
    </caption>

    <tr>
        <th><?= L('TITLE') ?></th>
        <th><?= L('TYPE') ?></th>
        <th><?= L('REPO_URL') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($projects) && iteratable($projects)) { ?>
    <?php foreach ($projects as $project) { ?>
    <tr>
        <td><?= $project->name ?></td>
        <td><?= $project->type ?></td>
        <td><?= $project->url ?></td>
        <td>
            <a href="/dep/admin/projects/<?= $project->id ?>">
                <button><?= L('EDIT') ?></button>
            </a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
