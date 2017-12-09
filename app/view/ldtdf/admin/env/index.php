<?= $this->layout('main') ?>
<?= $this->title([L('ENVIRONMENT_MANAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="/dep/admin/envs/new">
            <button><?= L('ADD_ENVIRONMENT') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption class="table-title">
        <?= L('ENVIRONMENT_LIST') ?>
    </caption>

    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('HOST') ?></th>
        <th><?= L('TITLE') ?></th>
        <th>
            <?= L('TYPE') ?>
            <select
            name="type"
            class="filter-name-by-value-select"
            id="env-types-filter"
            required>
                <option value="all"><?= L('ALL') ?></option>
                <?php if (($types = share('env-types')) && iteratable($types)) { ?>
                <?php foreach ($types as $_type) { ?>
                <option
                <?php if ($_type == $type) { ?>
                selected
                <?php } ?>
                value="<?= $_type ?>">
                    <?= L($_type) ?>
                </option>
                <?php } ?>
                <?php } ?>
            </select>
        </th>
        <th><?= L('BRANCH'), '/', L('TASK') ?></th>
        <th>
            <?= L('STATUS') ?>
            <select
            name="status"
            class="filter-name-by-value-select"
            id="env-status-filter"
            required>
                <option value="all"><?= L('ALL') ?></option>
                <?php if (($stat = share('env-status')) && iteratable($stat)) { ?>
                <?php foreach ($stat as $_stat) { ?>
                <option
                <?php if ($status == $_stat) { ?>
                selected
                <?php } ?>
                value="<?= $_stat ?>">
                    <?= L($_stat) ?>
                </option>
                <?php } ?>
                <?php } ?>
            </select>
        </th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>
    
    <?php if (isset($envs) && iteratable($envs)) { ?>
    <?php foreach ($envs as $key => $env) { ?>
    <tr>
        <td><?= $key+1 ?></td>
        <td><?= $env->host ?></td>
        <td><?= $env->name ?></td>
        <td><?= L($env->type) ?></td>
        <td><?= $env->getTaskBranchHTML() ?></td>
        <td>
            <span class="status-<?= $env->status ?>">
                <?= L($env->status) ?>
            </span>
        </td>
        <td>
            <a href="/dep/admin/envs/<?= $env->id ?>">
                <button><?= L('MANAGE') ?></button>
            </a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>
</table>

<?= $this->section('pagebar') ?>
