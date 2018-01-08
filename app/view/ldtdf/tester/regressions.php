<?= $this->layout('main') ?>
<?= $this->title(ldtdf('WAITTING_REGRESSION_LIST')) ?>
<?= $this->section('common') ?>

<table>
    <caption><?= L('WAITTING_REGRESSION_LIST') ?></caption>
    <tr>
        <th><?= L('TYPE') ?></th>
        <th><?= L('PROJECT') ?></th>
        <th><?= L('ENV') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($envs) && iteratable($envs)): ?>
    <?php foreach ($envs as $env): ?>
    <tr>
        <td>Web</td>
        <td>
            <a href='<?= lrn("test/regressions/env/{$env->id}") ?>'>
                <?= $env->project('name') ?? L('UNKNOWN') ?>
            </a>
        </td>
        <td><?= $env->host ?? L('UNKNOWN') ?></td>
        <td>
            <a href='<?= lrn("test/regressions/env/<?= $env->id ?>/pass") ?>'>
                <button class="btn-ok"><?= L('PASS') ?></button>
            </a>
            <a href='<?= lrn("test/regressions/env/{$env->id}/unpass") ?>'>
                <button class="btn-delete"><?= L('UNPASS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>

    <?php if (isset($projects) && iteratable($projects)): ?>
    <?php foreach ($projects as $project): ?>
    <tr>
        <td>App</td>
        <td>
            <a href='<?= lrn("test/regressions/project/{$project->id}") ?>'>
                <?= $project->name ?? L('UNKNOWN') ?>
            </a>
        </td>
        <td>-</td>
        <td>
            <a href='<?= lrn("test/regressions/project/{$project->id}/pass") ?>'>
                <button class="btn-ok"><?= L('PASS') ?></button>
            </a>
            <a href='<?= lrn("test/regressions/project/{$project->id}/unpass") ?>'>
                <button class="btn-delete"><?= L('UNPASS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>