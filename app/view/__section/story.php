<div>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_PRODUCT') ?></small>
    <span class="text-info">]</span>

    <span><small>
        <i>
            <a href="<?= lrn('products/'.$story->product('id')) ?>">
                <?= $this->escape($story->product('name')) ?:
                    '<i class="text-info">'.L('NULL').'</i>'
                ?>
            </a>
        </i>
    </small></span>
</div>

<br>

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

<div>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('STORY_AC') ?></small>
    <span class="text-info">]</span>
    <dl>
        <?php if (isset($acceptances) && iteratable($acceptances)): ?>
        <?php foreach ($acceptances as $acceptance): ?>
        <dd>
            <input
            <?php if (ci_equal($acceptance->status, 'checked')): ?>
            checked
            <?php endif ?>
            <?php if ($untestable ?? true): ?>
            disabled
            <?php else: ?>
            class="ac-check-status"
            data-id="<?= $acceptance->id ?>"
            <?php endif ?>
            type="checkbox"
            name="ac_status">
            <?= $this->escape($acceptance->detail) ?>
        </dd>
        <?php endforeach ?>
        <?php endif ?>
    </dl>
</div>

<div id="story-others">
    <span class="text-info">[</span>
    <?= L('STORY_REMARKS') ?>
    <span class="text-info">]</span>
    <textarea
    id="story-others-md"
    style="display:none"><?= $story->extra ?></textarea>
    <span class="vertical-2"></span>
</div>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    function setACCheckStatus(id) {
        console.log(id)
    }
    $(function() {
        editormd.markdownToHTML("story-others", {
            markdown        : $('#story-others-md').val(),
            
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

        $('.ac-check-status').change(function () {
            let api = '<?= lrn("stories/{$story->id}/ac") ?>' + '/' + $(this).data().id
            $.post(api, {
                '__rftkn__' : '<?= csrf_token() ?>',
                'checked'   : this.checked
            }, function (ret) {
                console.log(ret)
            })
        })
    })
</script>