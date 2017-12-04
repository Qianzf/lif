<?= $this->layout('main') ?>
<?= $this->title([L('BUG_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/bugs/new">
                <?= L('ADD_BUG') ?>
            </a>
        </button>
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
        <th><?= L('OPERATIONS') ?></th>
    </tr>
    <?php if (isset($bugs) && iteratable($bugs)) : ?>
    <?php foreach ($bugs as $bug) : ?>
    <tr>
        <td><?= $bug->id ?></td>
        <td><?= $bug->title ?></td>
        <td><?= $bug->creator()->name ?></td>
        <td><?= $bug->create_at ?></td>
        <td><?= $bug->os ?></td>
        <td><?= $bug->platform ?></td>
        <td>
            <button>
                <a href="/dep/bugs/<?= $bug->id ?>"><?= L('DETAILS') ?></a>
            </button>
        </td>
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>
