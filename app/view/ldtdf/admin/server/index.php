<?= $this->layout('main') ?>
<?= $this->title([L('SERVER_MANAGE'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/admin/servers/new"><?= L('ADD_SERVER') ?></a>
        </button>
    </dd>
</dl>

<table>
    <caption>
        <?= L('SERVER_LIST') ?>
    </caption>

    <tr>
        <th><?= L('TITLE') ?></th>
        <th><?= L('ADDR') ?></th>
        <th><?= L('PORT') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>
    
    <?php if (isset($servers) && iteratable($servers)) { ?>
    <?php foreach ($servers as $server) { ?>
    <tr>
        <td><?= $server->name ?></td>
        <td><?= $server->host ?></td>
        <td><?= $server->port ?></td>
        <td>
            <button>
                <a href="/dep/admin/servers/<?= $server->id ?>">
                    <?= L('MANAGE') ?>
                </a>
            </button>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>
</table>

<?= $this->section('pagebar') ?>
