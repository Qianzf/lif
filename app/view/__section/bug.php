<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('BUG_DETAILS') ?></small>
    <span class="text-info">]</span>

    <ul class="bug-detail-list">
        <li>
            <span><?= L('BUG_HOW') ?> ？</span>
            <p><?= $this->escape($bug->how) ?></p>
        </li>
        <li>
            <span><?= L('BUG_WHAT') ?> ？</span>
            <p><?= $this->escape($bug->what) ?></p>
        </li>
        <li>
            <span><?= L('RELATED_PRODUCT') ?></span>
            <span><i>
                <?php if ($product = $bug->product()): ?>
                <a href="<?= lrn('products/'.$product->id) ?>">
                    <?= $this->escape($product->name) ?>
                </a>
                <?php else: ?>
                <i class="text-info"><?= L('NULL') ?></i>
                <?php endif ?>
            </i></span>
        </li>
        <li>
            <span><?= L('ERROR_MSG') ?></span>：
            <span><?= $this->escape($bug->errmsg) ?></span>
        </li>
        <li>
            <span><?= L('ERROR_CODE') ?></span>：
            <span><?= $this->escape($bug->errcode) ?></span>
        </li>
        <li>
            <span><?= L('OS') ?></span>：
            <span><?= $this->escape($bug->os) ?></span>
        </li>
        <li>
            <span><?= L('OS_VERSION') ?></span>：
            <span><?= $this->escape($bug->os_ver) ?></span>
        </li>
        <li>
            <span><?= L('PLATFORM') ?></span>：
            <span><?= $this->escape($bug->platform) ?></span>
        </li>
        <li>
            <span><?= L('PLATFORM_VERSION') ?></span>：
            <span><?= $this->escape($bug->platform_ver) ?></span>
        </li>
        <li>
            <span><?= L('RECURABLE') ?></span>：
            <span><?= L($bug->recurable) ?></span>
        </li>
    </ul>
</p>

<?php if ($bug->extra): ?>
<div id="bug-extra">
    <span class="text-info">[</span>
    <span><?= L('BUG_REMARKS') ?></span>
    <span class="text-info">]</span>
    <span class="vertical-2"></span>
    <textarea
    id="bug-extra-md"
    style="display:none"><?= $this->escape($bug->extra) ?></textarea>
    <span class="vertical-2"></span>
</div>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    $(function() {
        editormd.markdownToHTML("bug-extra", {
            markdown : $('#bug-extra-md').val(),
            tocm : true,
            markdownSourceCode : true,
            emoji : true,
        })
    })
</script>
<?php endif ?>