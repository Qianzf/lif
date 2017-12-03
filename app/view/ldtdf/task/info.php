<?= $this->layout('main') ?>
<?= $this->title([L('VIEW_TASK'), L('LDTDFMS')]) ?>
<?= $this->section('common')  ?>

<h4>
    <?= L('VIEW_TASK') ?>
    <span class="stub"></span>
    <small><code>
        T<?= $task->id ?>
    </code></small>

    <em><?= $story->title ?></em>

    <?php if (isset($editable) && $editable): ?>
    <button>
        <a href="/dep/tasks/<?= $task->id ?>/edit"><?= L('EDIT') ?></a>
    </button>
    <?php endif ?>

    <?php if (isset($confirmable) && $confirmable): ?>
    <form
    method="POST"
    action="/dep/tasks/<?= $task->id?>/confirm"
    class="inline">
        <?= csrf_feild() ?>
        <input type="submit" value="<?= L('CONFIRM') ?>">
    </form>
    <?php endif ?>
    <?php if (isset($assignable) && $assignable): ?>
        <?php $dependency = in_array(strtoupper($task->status), [
                'DEVING',
                'WAITTING_DEV',
                'WAITTING_FIX_TEST',
            ]) ? '-with-dependencies' : '';
        ?>
        <?= $this->section("assign-form{$dependency}", [
            'model'  => $task,
            'branch' => $task->branch,
            'assignNotes' => $task->notes,
            'key'   => 'TASK',
            'api'   => "/dep/tasks/{$task->id}/users/assignable",
            'route' => "/dep/tasks/{$task->id}/assign"
        ]) ?>
    <?php endif ?>

    <?php if (isset($activeable) && $activeable): ?>
    <form
    method="POST"
    action="/dep/tasks/<?= $task->id ?>/activate"
    class="inline" id="activate-task-form">
        <?= csrf_feild() ?>
        <input
        onclick="activateTaskConfirm()"
        type="button"
        class="text-todo"
        value="<?= L('ACTIVATE_TASK') ?>">
        <input type="hidden" name="activate_reason">
    </form>
    <script type="text/javascript">
        function activateTaskConfirm() {
            let reason = prompt(
                "<?= L('INPUT_ACTIVATE_REASON'), ' (', L('OPTIONAL'), ')' ?>"
            )

            if (reason !== null) {
                $('input[name="activate_reason"]').val(reason)
                $('#activate-task-form').submit()
            }
        }
    </script>
    <?php endif ?>

    <?php if (isset($cancelable) && $cancelable): ?>
    <form
    method="POST"
    action="/dep/tasks/<?= $task->id ?>/cancel"
    class="inline" id="cancel-task-form">
        <?= csrf_feild() ?>
        <input
        onclick="cancelTaskConfirm()"
        type="button"
        class="text-danger"
        value="<?= L('CANCEL_TASK') ?>">
        <input type="hidden" name="cancel_reason">
    </form>
    <script type="text/javascript">
        function cancelTaskConfirm() {
            let reason = prompt(
                "<?= L('INPUT_CANCEL_REASON'), ' (', L('OPTIONAL'), ')' ?>"
            )

            if (reason !== null) {
                $('input[name="cancel_reason"]').val(reason)
                $('#cancel-task-form').submit()
            }
        }
    </script>
    <?php endif ?>
</h4>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_STATUS') ?></small>
    <span class="text-info">]</span>
    <?php if ($task->status) : ?>
    <button class="btn-info"><?= L("STATUS_{$task->status}") ?></button>
    <?php endif ?>
</p>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_PROJECT') ?></small>
    <span class="text-info">]</span>
    <i>
        <a href="/dep/projects/<?= $project->id ?>">
            <?= $project->name, " ({$project->type})"?>
        </a>
    </i>
</p>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_STORY') ?></small>
    <span class="text-info">]</span>

    <i>
        <a href="/dep/stories/<?= $story->id ?>">
            S<?= $story->id ?>:
            <?= $story->title ?>
        </a>
    </i>
</p>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_DETAILS') ?></small>
    <span class="text-info">]</span>
</p>

<blockquote><em>
    <p>
        <?= L('STORY_WHO') ?>
        <span><?= $story->role ?></span>
    </p>
    <p>
        <?= L('STORY_WHAT') ?>
        <span><?= $story->activity ?></span>
    </p>
    <p>
        <?= L('STORY_FOR') ?>
        <span><?= $story->value ?></span>
    </p>
</em></blockquote>

<div id="task-acceptances">
    <span class="text-info">[</span>
    <b><?= L('STORY_AC') ?></b>
    <span class="text-info">]</span>
</div>
<textarea
id="task-acceptances-md"
style="display:none"><?= $this->escape($story->acceptances) ?></textarea>

<?php if (trim($story->extras)): ?>
<div id="task-others">
    <span class="text-info">[</span>
    <b><?= L('STORY_NOTES') ?></b>
    <span class="text-info">]</span>
</div>
<textarea
id="task-others-md"
style="display:none"><?= $this->escape($story->extra) ?></textarea>
<?php endif ?>

<?php if (trim($task->notes)): ?>
<div id="task-notes">
    <span class="text-info">[</span>
    <b><?= L('TASK_NOTES') ?></b>
    <span class="text-info">]</span>
</div>
<textarea
id="task-notes-md"
style="display:none"><?= $this->escape($task->notes) ?></textarea>
<?php endif ?>

<?php if (trim($this->escape($task->branch))): ?>
<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_BRANCH') ?></small>
    <span class="text-info">]</span>

    <code><?= $task->branch ?></code>
</p>
<?php endif ?>

<?php if ($task->env): ?>
<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_ENV') ?></small>
    <span class="text-info">]</span>

    <code><?= $task->env ?></code>
</p>
<?php endif ?>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_TASK') ?></small>
    <span class="text-info">]</span>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <ul>
        <?php foreach ($tasks as $task): ?>
        <li>
            <a href="/dep/tasks/<?= $task->id ?>">
                T<?= $task->id ?>:
                <?= $task->project()->name ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>
</p>

<?= $this->section('trendings-with-sort', [
    'model' => $task,
    'displayRefType'  => true,
    'displayRefState' => true,
    'displayComments' => true,
]) ?>
<?= $this->section('lib/editormd') ?>
<!-- <?= $this->section('comment') ?> -->

<script type="text/javascript">
    editormd.markdownToHTML("task-acceptances", {
        markdown        : $('#task-acceptances-md').val(),
        // 开启 HTML 标签解析，为了安全性，默认不开启
        // htmlDecode      : true,
        // you can filter tags decode
        // htmlDecode      : "style,script,iframe",      
        // toc             : false,
        tocm            : true,    // Using [TOCM]
        // 自定义 ToC 容器层
        //tocContainer    : "#custom-toc-container",
        //gfm             : false,
        //tocDropdown     : true,
        // 是否保留 Markdown 源码，即是否删除保存源码的 Textarea 标签
        markdownSourceCode : true,
        // emoji           : true,
        // taskList        : true,
        // tex             : true,  // 默认不解析
        // flowChart       : true,  // 默认不解析
        // sequenceDiagram : true,  // 默认不解析
    })

    editormd.markdownToHTML("task-others", {
        markdown : $('#task-others-md').val(),
        // htmlDecode : "style,script,iframe",
        tocm : true,
        markdownSourceCode : true
    });

    editormd.markdownToHTML("task-notes", {
        markdown : $('#task-notes-md').val(),
        // htmlDecode : "style,script,iframe",
        tocm : true,
        markdownSourceCode : true
    });
</script>
