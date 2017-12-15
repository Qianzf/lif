<?= $this->layout('main') ?>
<?= $this->title([L('BUG_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="/dep/bugs/new">
            <button><?= L('ADD_BUG') ?></button>
        </a>
    </dd>
</dl>

<table>
    <caption><?= L('BUG_LIST') ?></caption>

    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('TITLE') ?></th>
        <th><?= L('CREATOR') ?></th>
        <th><?= L('TIME') ?></th>
        <th><?= L('OS') ?></th>
        <th><?= L('PLATFORM') ?></th>
    </tr>
    <?php if (isset($bugs) && iteratable($bugs)) : ?>
    <?php foreach ($bugs as $bug) : ?>
    <tr>
        <td><?= $bug->id ?></td>
        <td>
            <a href="/dep/bugs/<?= $bug->id ?>">
                <?= $bug->title ?>
            </a>
        </td>
        <td><?= $bug->creator('name') ?></td>
        <td><?= $bug->create_at ?></td>
        <td><?= $bug->os ?></td>
        <td><?= $bug->platform ?></td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
