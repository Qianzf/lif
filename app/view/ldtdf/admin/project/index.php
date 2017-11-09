<?= $this->layout('main') ?>
<?= $this->title([lang('PROJECT_MANAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/admin/projects/new"><?= lang('ADD_PROJECT') ?></a>
        </button>
    </dd>
</dl>

<table>
    <caption>
        <?= lang('PROJECT_LIST') ?>
    </caption>

    <tr>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('REPO_URL') ?></th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($projects) && iteratable($projects)) { ?>
    <?php foreach ($projects as $project) { ?>
    <tr>
        <td><?= $project->name ?></td>
        <td><?= $project->url ?></td>
        <td>
            <button>
                <a href="/dep/admin/projects/<?= $project->id ?>">
                    <?= lang('DETAILS') ?>
                </a>
            </button>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<?= $this->section('pagebar') ?>
