<?= $this->layout('main') ?>
<?= $this->title(ldtdf('SERVER_MANAGE')) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="<?= lrn('admin/servers/new') ?>">
            <button><?= L('ADD_SERVER') ?></button>
        </a>
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
            <a href='<?= lrn("admin/servers/{$server->id}") ?>'>
                <button><?= L('EDIT') ?></button>
            </a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>
</table>

<?= $this->section('pagebar') ?>
