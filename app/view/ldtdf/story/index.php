<?= $this->layout('main') ?>
<?= $this->title([L('STORY_LIST'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <a href="/dep/stories/new">
            <button><?= L('ADD_STORY') ?></button>
        </a>
    </dd>
</dl>

<?php if (isset($stories) && iteratable($stories)): ?>
<table>
    <caption><?= L('STORY_LIST') ?></caption>

    <tr>
        <th><?= L('ID') ?></th>
        <th><?= L('TITLE') ?></th>
        <th><?= L('CREATOR') ?></th>
        <th><?= L('TIME') ?></th>
        <th><?= L('OPERATIONS') ?></th>
    </tr>
    <?php foreach ($stories as $story): ?>
    <tr>
        <td><?= $story->id ?></td>
        <td><?= $story->title ?></td>
        <td>
            <a href="/dep/users/<?= $story->creator('id') ?>">
                <?= $story->creator('name') ?>
            </a>
        </td>
        <td><?= $story->create_at ?></td>
        <td>
            <a href="/dep/stories/<?= $story->id ?>">
                <button><?= L('DETAILS') ?></button>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>
