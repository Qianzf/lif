<?= $this->layout('main') ?>
<?= $this->title([lang('VIEW_TASK'), lang('LDTDFMS')]) ?>
<?= $this->section('common')  ?>

<h2>
    <span class="stub"></span>
    <small><code>
        <?= $task->id ?>
    </code></small>
    <?= $task->title ?>
</h2>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= lang('TASK_STATUS') ?></small>
    <span class="text-info">]</span>
    <?php if ($task->status) : ?>
    <button class="btn-info"><?= lang("TASK_{$task->status}") ?></button>
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
    <small><?= lang('TASK_DETAILS') ?></small>
    <span class="text-info">]</span>
</p>

<?php if ('no' == $task->custom) : ?>
    <p>
        <span class="stub-3"></span>
        <em><a href="<?= $task->url ?>">
            <?= $task->url ?>
        </a></em>
    </p>
<?php else : ?>
    <blockquote><em>
        <p>
            <?= lang('STORY_WHO') ?>
            <span><?= $task->story_role ?></span>
        </p>
        <p>
            <?= lang('STORY_WHAT') ?>
            <span><?= $task->story_activity ?></span>
        </p>
        <p>
            <?= lang('STORY_FOR') ?>
            <span><?= $task->story_value ?></span>
        </p>
    </em></blockquote>
    <div id="task-acceptances">
        <span class="text-info">[</span>
        <b><?= lang('STORY_AC') ?></b>
        <span class="text-info">]</span>
    </div>
    <div id="task-others">
        <span class="text-info">[</span>
        <b><?= lang('OTHER_NOTES') ?></b>
        <span class="text-info">]</span>
    </div>

    <textarea
    id="task-acceptances-md"
    style="display:none"><?= $task->acceptances ?></textarea>
    <textarea
    id="task-others-md"
    style="display:none"><?= $task->extra ?></textarea>
<?php endif ?>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= lang('TASK_TRENDING') ?></small>
    <span class="text-info">]</span>
    <ul>
        <li>
            <span><?= $task->create_at ?></span>
            <a href="/dep/user/<?= $task->creator ?>">
                <?= $task->creator()->name ?>
            </a>
            <span><?= lang('CREATED') ?></span>
        </li>
    </ul>
</p>

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
</script>
