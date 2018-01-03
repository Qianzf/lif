<?= $this->layout('main') ?>

<?php if (isset($task) && is_object($task)) { ?>
<?php $tid = $task->alive()      ? $task->id : 'new'; ?>
<?php $origin = $story->alive()  ? $story : $bug; ?>
<?php $searchAPI = $bug->alive() ? 'bugs' : 'stories'; ?>

<?= $this->section('back2list', [
    'model'  => $task,
    'key'    => 'TASK',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/tasks',
]) ?>

<form method="POST" action="/dep/tasks/<?= $tid ?>">
    <?= csrf_feild() ?>

    <label>
        <span class="label-title"><?= L('TASK_ORIGIN') ?></span>
        <input
        <?php if ($searchAPI == 'stories'): ?>
        checked
        <?php endif ?>
        type="radio"
        name="origin_type"
        value="story">
        <span><?= L('STORY') ?></span>
        <input
        <?php if ($searchAPI == 'bugs'): ?>
        checked
        <?php endif ?>
        type="radio"
        name="origin_type"
        value="bug">
        <span><?= L('BUG') ?></span>
    </label>

    <label>
        <span class="label-title" id="task-origin-title">
            <?= L("RELATED_{$searchAPI}") ?>
        </span>
        <input type="hidden" name="origin_id" value="<?= $origin->id ?>">
        <?= $this->section('instant-search', [
            'api' => "/dep/tasks/{$searchAPI}/attachable",
            'oldVal' => $origin->title,
            'sresKeyInput' => 'origin_id',
            // 'sresKey' => 'id',
            'sresVal' => 'title',
        ]) ?>
    </label>

    <label>
        <span class="label-title">
            <?= L('RELATED_PROJECT') ?>
        </span>
        <select name="project" required>
            <option>-- <?= L('SELECT_PROJECT') ?> --</option>
            <?php foreach ($projects as $proj): ?>
                <option
                <?php if ($project->id == $proj->id): ?>
                selected
                <?php endif ?>
                value="<?= $proj->id ?>">
                    <?= $proj->name, " ($proj->type)" ?>
                </option>
            <?php endforeach ?>
        </select>
    </label>

    <label>
        <span class="label-title"><?= L('REMARKS') ?></span>
        <div
        id="task-notes"
        class="editormd editormd-vertical">
            <textarea
            class="editormd-markdown-textarea"
            placeholder="<?= L('TASK_NOTES') ?>"
            name="notes"><?= $task->notes ?></textarea>
        </div>
    </label>

    <?php if ($editable ?? false) : ?>
    <?= $this->section('submit', [
        'model' => $task
    ]) ?>
    <?php endif ?>
</form>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    $('input[name="origin_type"]').change(function () {
        let title = "<?= L('RELATED_STORY') ?>"
        let searchApi = '/dep/tasks/stories/attachable'

        if ('bug' == this.value) {
            searchApi = '/dep/tasks/bugs/attachable'
            title = "<?= L('RELATED_BUG') ?>"
        }

        $('#selected-search-res span').html('')
        $('#instant-search-bar').val('')
        $('#instant-search-res-list').html('')
        $('#instant-search-and-show').hide()
        $('#instant-search-api').val(searchApi)
        $('#task-origin-title').html(title)
    })
    var EditorMDObjects = [
    {
        id : 'task-notes',
        placeholder : "<?=
            L('NOTES'),
            ' / ',
            L('ATTACHMENT'),
            L('ETC')
        ?>"
    }
    ]
    $(function() {
        tryDisplayEditormd()
    })
    function removeAllSelectedResult() {
        $('input[name="origin_id"]').val('')
    }
</script>
<?php } ?>