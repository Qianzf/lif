<?= $this->layout('main') ?>
<?= $this->title([L('PROJECT_MANAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/admin/projects/new"><?= L('ADD_PROJECT') ?></a>
        </button>
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
            <button>
                <a href="/dep/admin/projects/<?= $project->id ?>">
                    <?= L('DETAILS') ?>
                </a>
            </button>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
