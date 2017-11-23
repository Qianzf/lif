<?= $this->layout('main') ?>
<?= $this->title([lang('VIEW_TASK'), lang('LDTDFMS')]) ?>
<?= $this->section('common')  ?>

<h2>
    <span class="text-info">[</span>
    <small><code>
        #<?= $task->id ?>
    </code></small>
    <?= $task->title ?>
    <span class="text-info">]</span>
    <sup><button class="btn-info">
        <?= lang("TASK_{$task->status}") ?>
    </button></sup>
</h2>

<span class="vertical"></span>

<?php if ('no' == $task->custom) : ?>
    <p>
        <span><?= lang('TASK_DETAILS'), ': ' ?></span>
        <em><a href="<?= $task->url ?>">
            <?= $task->url ?>
        </a></em>
    </p>
<?php else : ?>
    <blockquote><em>
        <?=
            lang('STORY_WHO'), ' ' , $task->story_role, ', ',
            lang('STORY_WHAT'), ' ', $task->story_activity, ', ',
            lang('STORY_FOR'), ' ', $task->story_value
        ?>
    </em></blockquote>
    <div id="task-acceptances">
        <b><?= lang('STORY_AC') ?></b>
    </div>
    <div id="task-others"></div>

    <textarea
    id="task-acceptances-md"
    style="display:none"><?= $task->acceptances ?></textarea>
    <textarea
    id="task-others-md"
    style="display:none"><?= $task->extra ?></textarea>
<?php endif ?>

<?= css([
    'editor.md/css/editormd.min',
    'editor.md/css/editormd.preview.min',
    'editor.md/lib/codemirror/codemirror.min',
    'editor.md/lib/codemirror/addon/dialog/dialog',
    'editor.md/lib/codemirror/addon/search/matchesonscrollbar',
]) ?>

<?= js([
    'editor.md/editormd.min',
    'editor.md/lib/codemirror/codemirror.min',
    'editor.md/lib/codemirror/modes.min',
    'editor.md/lib/codemirror/addons.min',
    'editor.md/lib/marked.min',
    'editor.md/lib/prettify.min',
]) ?>

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
