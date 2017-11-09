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
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('HOST') ?></th>
        <th>
            <?= lang('TYPE') ?>
            <select name="type" id="env-types-filter" required>
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
        <th><?= lang('OPERATIONS') ?></th>
    </tr>
    
    <?php if (isset($envs) && iteratable($envs)) { ?>
    <?php foreach ($envs as $env) { ?>
    <tr>
        <td><?= $env->name ?></td>
        <td><?= $env->host ?></td>
        <td><?= lang($env->type) ?></td>
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
