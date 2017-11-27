<?= $this->layout('main') ?>
<?= $this->title([lang('VIEW_STORY'), lang('LDTDFMS')]) ?>
<?= $this->section('common')  ?>

<h4>
    <?= lang('VIEW_STORY') ?>

    <span class="stub"></span>
    <small><code>
        S<?= $story->id ?>
    </code></small>

    <em><?= $story->title ?></em>

    <?php if (isset($editable) && $editable): ?>
    <button>
        <a href="/dep/stories/<?= $story->id ?>/edit"><?= lang('EDIT') ?></a>
    </button>
    <?php endif ?>
    <?php if (isset($assignable) && $assignable): ?>
        <button>
            <a href="/dep/tasks/new?story=<?= $story->id ?>">
                <?= lang('ASSIGN') ?>
            </a>
        </button>
    <?php endif ?>
</h4>

<div>
    <h5>
        <span class="stub-2"></span>
        <span class="text-info">[</span>
        <?= lang('STORY_DETAILS') ?>
        <span class="text-info">]</span>
    </h5>

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
</div>

<div id="story-acceptances">
    <h5>
        <span class="text-info">[</span>
        <?= lang('STORY_AC') ?>
        <span class="text-info">]</span>
    </h5>

    <textarea
    id="story-acceptances-md"
    style="display:none"><?= $story->acceptances ?></textarea>
</div>

<div id="story-others">
    <h5>
        <span class="text-info">[</span>
        <?= lang('OTHER_NOTES') ?>
        <span class="text-info">]</span>
    </h5>

    <textarea
    id="story-others-md"
    style="display:none"><?= $story->extra ?></textarea>
</div>

<div>
    <h5>
        <span class="stub-2"></span>
        <span class="text-info">[</span>
        <?= lang('RELATED_TASK') ?>
        <span class="text-info">]</span>
    </h5>

    <?php if (isset($tasks) && iteratable($tasks)): ?>
    <ul>
        <?php foreach ($tasks as $task): ?>
        <li>
            <a href="/dep/tasks/<?= $task->id ?>">
                #<?= $task->id ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>
</div>


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
