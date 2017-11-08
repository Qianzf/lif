<?= $this->layout('main') ?>
<?= $this->title([lang('SERVER_MANAGE'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<ul>
    <li>
        <a href="/dep/admin/servers/new"><?= lang('ADD_SERVER') ?></a>
    </li>
</ul>

<table>
    <caption>
        <?= lang('SERVER_LIST') ?>
    </caption>

    <tr>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('ADDR') ?></th>
        <th><?= lang('PORT') ?></th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>
    
    <?php if (isset($servers) && iteratable($servers)) { ?>
    <?php foreach ($servers as $server) { ?>
    <tr>
        <td><?= $server->name ?></td>
        <td><?= $server->host ?></td>
        <td><?= $server->port ?></td>
        <td>
            <a href="/dep/admin/servers/<?= $server->id ?>">
                <?= lang('MANAGE') ?>
            </a>
        </td>
    </tr>
    <?php } ?>
    <?php } ?>
</table>

<?= $this->section('pagebar') ?>
