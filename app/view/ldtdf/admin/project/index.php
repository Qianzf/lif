<?= $this->layout('main') ?>
<?= $this->title([lang('PROJECT_MANAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<ul>
    <li>
        <a href="/dep/admin/projects/new"><?= lang('ADD_PROJECT') ?></a>
    </li>
</ul>

<?= $this->section('search') ?>

<br>

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
            <a href="/dep/admin/projects/<?= $project->id ?>">
                <?= lang('DETAILS') ?>
            </a>
            <a href="/dep/admin/envs?type="></a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>

</table>

<br>

<?= $this->section('pagebar') ?>
