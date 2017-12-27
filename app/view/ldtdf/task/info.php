<?= $this->layout('main') ?>
<?= $this->title([L('VIEW_TASK'), L('LDTDFMS')]) ?>
<?= $this->section('common')  ?>

<h4>
    <?= L('VIEW_TASK') ?>
    <span class="stub"></span>
    <small><code>
        T<?= $task->id ?>
    </code></small>

    <em><?= $origin->title ?></em>

    <?php if (isset($editable) && $editable): ?>
    <a href="/dep/tasks/<?= $task->id ?>/edit">
        <button><?= L('EDIT') ?></button>
    </a>
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
        <?php $dependency = ($deployable ?? false) ? '-with-dependencies' : '';
        ?>
        <?= $this->section("assign-form{$dependency}", [
            'model'    => $task,
            'branch'   => $task->branch,
            'config'   => $task->config,
            'remarks'  => $task->deploy,
            'manually' => (strtolower($task->manually) == 'yes'),
            'key'      => 'TASK',
            'api'      => "/dep/tasks/{$task->id}/users/assignable",
            'route'    => "/dep/tasks/{$task->id}/assign"
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
    <button class="btn-info"><?= L("STATUS_{$task->status}") ?></button>
    <i><small>(<?= $task->current('name') ?>)</small></i>
</p>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_PROJECT') ?></small>
    <span class="text-info">]</span>
    <small class="text-task"><i>
        <?= L($project->type) ?>
        <code>P<?= $project->id ?></code>
        <a href="/dep/projects/<?= $project->id ?>">
            <?=  $project->name ?>
        </a>
    </i></small>
</p>

<?= $this->section("ldtdf/task/{$task->origin_type}", [], true) ?>

<?php if ($task->notes): ?>
<div id="task-notes">
    <span class="text-info">[</span>
    <span><?= L('TASK_REMARKS') ?></span>
    <span class="text-info">]</span>
</div>
<textarea
id="task-notes-md"
style="display:none"><?= $this->escape($task->notes) ?></textarea>
<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    $(function() {
        editormd.markdownToHTML("task-notes", {
            markdown : $('#task-notes-md').val(),
            tocm : true,
            markdownSourceCode : true,
            emoji : true,
        })
    })
</script>
<?php endif ?>


<?php if ($task->branch = trim($task->branch)): ?>
<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_BRANCH') ?></small>
    <span class="text-info">]</span>

    <code><?= $this->escape($task->branch) ?></code>
</p>
<?php endif ?>

<?php if ($task->deploy = trim($task->deploy)): ?>
<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('MANUALLY_DEPLOY') ?></small>
    <span class="text-info">]</span>

    <blockquote><?= $this->escape($task->deploy) ?></blockquote>
</p>
<?php endif ?>

<?php if (($env = $task->environment()) && $env->alive()): ?>
<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('TASK_ENV') ?></small>
    <span class="text-info">]</span>

    <code><?= $env->host ?></code>
</p>
<?php endif ?>

<?= $this->section('related-tasks') ?>
<?= $this->section('trendings-with-sort', [
    'model' => $task,
    'object' => 'TASK',
    'displayRefType' => false,
]) ?>