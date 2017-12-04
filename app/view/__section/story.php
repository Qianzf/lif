<div>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('STORY_DETAILS') ?></small>
    <span class="text-info">]</span>

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
</div>

<div id="story-acceptances">
    <span class="text-info">[</span>
    <?= L('STORY_AC') ?>
    <span class="text-info">]</span>

    <textarea
    id="story-acceptances-md"
    style="display:none"><?= $story->acceptances ?></textarea>
</div>

<div id="story-others">
    <span class="text-info">[</span>
    <?= L('STORY_REMARKS') ?>
    <span class="text-info">]</span>

    <textarea
    id="story-others-md"
    style="display:none"><?= $story->extra ?></textarea>
</div>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    $(function() {
        editormd.markdownToHTML("story-acceptances", {
            markdown        : $('#story-acceptances-md').val(),
            
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

        editormd.markdownToHTML("story-others", {
            markdown : $('#story-others-md').val(),
            htmlDecode : "style,script,iframe",
            tocm : true,
            markdownSourceCode : true
        })
    })
</script>