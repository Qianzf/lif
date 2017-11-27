<?= $this->layout('main') ?>
<?= $this->title([lang('VIEW_STORY'), lang('LDTDFMS')]) ?>
<?= $this->section('common')  ?>

<h2>
    <span class="stub"></span>
    <small><code>
        S<?= $story->id ?>
    </code></small>

    <?= $story->title ?>

    <?php if (isset($editable) && $editable): ?>
    <button>
        <a href="/dep/stories/<?= $story->id ?>/edit"><?= lang('EDIT') ?></a>
    </button>
    <?php endif ?>
    <?php if (isset($assignable) && $assignable): ?>
        <?= $this->section('assign-form', [
            'model' => $story,
            'key'   => 'STORY',
            'route' => "/dep/stories/{$story->id}/assign"
        ]) ?>
    <?php endif ?>
</h2>

<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= lang('STORY_DETAILS') ?></small>
    <span class="text-info">]</span>
</p>

<?php if ('no' == $story->custom) : ?>
    <p>
        <span class="stub-3"></span>
        <em><a href="<?= $story->url ?>">
            <?= $story->url ?>
        </a></em>
    </p>
<?php else : ?>
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
    <div id="story-acceptances">
        <span class="text-info">[</span>
        <b><?= lang('STORY_AC') ?></b>
        <span class="text-info">]</span>
    </div>
    <div id="story-others">
        <span class="text-info">[</span>
        <b><?= lang('OTHER_NOTES') ?></b>
        <span class="text-info">]</span>
    </div>

    <textarea
    id="story-acceptances-md"
    style="display:none"><?= $story->acceptances ?></textarea>
    <textarea
    id="story-others-md"
    style="display:none"><?= $story->extra ?></textarea>
<?php endif ?>

<?= $this->section('trendings-with-sort', [
    'model'  => $story,
    'object' => 'STORY',
]) ?>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
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
    });
</script>
