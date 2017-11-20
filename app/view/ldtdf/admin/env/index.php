<?= $this->layout('main') ?>
<?= $this->title([lang('ENVIRONMENT_MANAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/admin/envs/new"><?= lang('ADD_ENVIRONMENT') ?></a>
        </button>
    </dd>
</dl>

<table>
    <caption class="table-title">
        <?= lang('ENVIRONMENT_LIST') ?>
    </caption>

    <tr>
        <th><?= lang('ID') ?></th>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('HOST') ?></th>
        <th>
            <?= lang('TYPE') ?>
            <select
            name="type"
            class="filter-name-by-value-select"
            id="env-types-filter"
            required>
                <option value="all"><?= lang('ALL') ?></option>
                <?php if (($types = share('env-types')) && iteratable($types)) { ?>
                <?php foreach ($types as $_type) { ?>
                <option
                <?php if ($_type == $type) { ?>
                selected
                <?php } ?>
                value="<?= $_type ?>">
                    <?= lang($_type) ?>
                </option>
                <?php } ?>
                <?php } ?>
            </select>
        </th>
        <th>
            <?= lang('STATUS') ?>
            <select
            name="status"
            class="filter-name-by-value-select"
            id="env-status-filter"
            required>
                <option value="all"><?= lang('ALL') ?></option>
                <?php if (($stat = share('env-status')) && iteratable($stat)) { ?>
                <?php foreach ($stat as $_stat) { ?>
                <option
                <?php if ($status == $_stat) { ?>
                selected
                <?php } ?>
                value="<?= $_stat ?>">
                    <?= lang($_stat) ?>
                </option>
                <?php } ?>
                <?php } ?>
            </select>
        </th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>
    
    <?php if (isset($envs) && iteratable($envs)) { ?>
    <?php foreach ($envs as $key => $env) { ?>
    <tr>
        <td><?= $key+1 ?></td>
        <td><?= $env->name ?></td>
        <td><?= $env->host ?></td>
        <td><?= lang($env->type) ?></td>
        <td>
            <span class="status-<?= $env->status ?>">
                <?= lang($env->status) ?>
            </span>
        </td>
        <td>
            <button>
                <a href="/dep/admin/envs/<?= $env->id ?>">
                    <?= lang('MANAGE') ?>
                </a>
            </button>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>
</table>

<?= $this->section('pagebar') ?>
