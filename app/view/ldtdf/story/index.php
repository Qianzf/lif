<?= $this->layout('main') ?>
<?= $this->title([lang('STORY_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<dl class="list">
    <dd>
        <button>
            <a href="/dep/stories/new">
                <?= lang('ADD_STORY') ?>
            </a>
        </button>
    </dd>
</dl>

<?php if (isset($stories) && iteratable($stories)): ?>
<table>
    <caption><?= lang('STORY_LIST') ?></caption>

    <tr>
        <th><?= lang('ID') ?></th>
        <th><?= lang('TITLE') ?></th>
        <th><?= lang('CREATOR') ?></th>
        <th><?= lang('TIME') ?></th>
        <th><?= lang('OPERATIONS') ?></th>
    </tr>
    <?php foreach ($stories as $story): ?>
    <tr>
        <td><?= $story->id ?></td>
        <td><?= $story->title ?></td>
        <td>
            <a href="/dep/users/<?= $story->creator()->id ?>">
                <?= $story->creator()->name ?>
            </a>
        </td>
        <td><?= $story->create_at ?></td>
        <td>
            <a href="/dep/stories/<?= $story->id ?>">
                <?= lang('DETAILS') ?>
            </a>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>
