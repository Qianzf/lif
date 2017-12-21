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

<table>
    <caption><?= L('STORY_LIST') ?></caption>

    <tr>
        <th
        class="time-sort"
        data-sort="<?= $_GET['sort'] ?? 'desc' ?>">
            <?= L('CREATE_TIME') ?>
        </th>
        <th><?= L('TITLE') ?></th>
        <th>
            <?= L('CREATOR') ?>
            <?= $this->section('filter/common', [
                'name'   => 'creator',
                'list'   => $users,
                'isUser' => true,
            ]) ?>
        </th>
    </tr>

    <?php if (isset($stories) && iteratable($stories)): ?>
    <?php foreach ($stories as $story): ?>
    <tr>
        <td><?= $story->create_at ?></td>
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
    </tr>
    <?php endforeach ?>
    <?php endif ?>
</table>

<?= $this->section('pagebar') ?>