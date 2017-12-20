<?= $this->layout('main') ?>
<?= $this->title([L('WAITTING_REGRESSION_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<table>
    <caption><?= L('WAITTING_REGRESSION_LIST') ?></caption>
    <tr>
        <th><?= L('ENV') ?></th>
        <th><?= L('PROJECT') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>

    <?php if (isset($regressions) && iteratable($regressions)): ?>
    <?php foreach ($regressions as $regression): ?>
    <tr>
        <td><?= $regression->host ?? L('UNKNOWN') ?></td>
        <td>
            <a href="/dep/test/regressions/<?= $regression->id ?>">
                <?= $regression->project('name') ?? L('UNKNOWN') ?>
            </a>
        </td>
        <td>
            <a href="/dep/test/regressions/<?= $regression->id ?>/unpass">
                <button class="btn-delete"><?= L('SET_UNPASS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>