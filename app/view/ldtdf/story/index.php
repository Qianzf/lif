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
    </tr>
    <?php foreach ($stories as $story): ?>
    <tr>
        <td><?= $story->id ?></td>
        <td>
            <a href="/dep/stories/<?= $story->id ?>">
                <?= $story->title ?>
            </a>
        </td>
        <td>
            <a href="/dep/users/<?= $story->creator('id') ?>">
                <?= $story->creator('name') ?>
            </a>
        </td>
        <td><?= $story->create_at ?></td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>
