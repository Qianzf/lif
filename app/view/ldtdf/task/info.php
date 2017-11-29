<?= $this->layout('main') ?>
<?= $this->title([lang('VIEW_TASK'), lang('LDTDFMS')]) ?>
<?= $this->section('common')  ?>

<h4>
    <?= lang('VIEW_TASK') ?>
    <span class="stub"></span>
    <small><code>
        T<?= $task->id ?>
    </code></small>

    <em><?= $story->title ?></em>

    <?php if (isset($editable) && $editable): ?>
    <button>
        <a href="/dep/tasks/<?= $task->id ?>/edit"><?= lang('EDIT') ?></a>
    </button>
    <?php endif ?>
    <?php if (isset($assignable) && $assignable): ?>
        <?php $dependency = ('WAITTING_DEV' === strtoupper($task->status))
            ? '-with-dependencies' : '';
        ?>
        <?= $this->section("assign-form{$dependency}", [
            'model' => $task,
            'key'   => 'TASK',
            'api'   => "/dep/tasks/{$task->id}/users/assignable",
            'route' => "/dep/tasks/{$task->id}/assign"
        ]) ?>
    <?php endif ?>
</h4>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= lang('TASK_STATUS') ?></small>
    <span class="text-info">]</span>
    <?php if ($task->status) : ?>
    <button class="btn-info"><?= lang("STATUS_{$task->status}") ?></button>
    <?php endif ?>
</p>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= lang('RELATED_PROJECT') ?></small>
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
    <small><?= lang('RELATED_STORY') ?></small>
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
    <small><?= lang('TASK_DETAILS') ?></small>
    <span class="text-info">]</span>
</p>

<blockquote><em>
    <p>
        <?= lang('STORY_WHO') ?>
        <span><?= $story->role ?></span>
    </p>
    <p>
        <?= lang('STORY_WHAT') ?>
        <span><?= $story->activity ?></span>
    </p>
    <p>
        <?= lang('STORY_FOR') ?>
        <span><?= $story->value ?></span>
    </p>
</em></blockquote>

<div id="task-acceptances">
    <span class="text-info">[</span>
    <b><?= lang('STORY_AC') ?></b>
    <span class="text-info">]</span>
</div>

<div id="task-others">
    <span class="text-info">[</span>
    <b><?= lang('STORY_NOTES') ?></b>
    <span class="text-info">]</span>
</div>

<div id="task-notes">
    <span class="text-info">[</span>
    <b><?= lang('TASK_NOTES') ?></b>
    <span class="text-info">]</span>
</div>

<textarea
id="task-acceptances-md"
style="display:none"><?= $story->acceptances ?></textarea>
<textarea
id="task-others-md"
style="display:none"><?= $story->extra ?></textarea>
<textarea
id="task-notes-md"
style="display:none"><?= $task->notes ?></textarea>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= lang('RELATED_TASK') ?></small>
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
    'displayShort' => true,
]) ?>
<?= $this->section('lib/editormd') ?>

<script type="text/javascript">
    editormd.markdownToHTML("task-acceptances", {
        markdown        : $('#task-acceptances-md').val(),
        
        // 开启 HTML 标签解析，为了安全性，默认不开启
        // htmlDecode      : true,
        // you can filter tags decode
        htmlDecode      : "style,script,iframe",
        
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
        htmlDecode : "style,script,iframe",
        tocm : true,
        markdownSourceCode : true
    });

    editormd.markdownToHTML("task-notes", {
        markdown : $('#task-notes-md').val(),
        htmlDecode : "style,script,iframe",
        tocm : true,
        markdownSourceCode : true
    });
</script>
