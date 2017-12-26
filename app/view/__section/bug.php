<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('BUG_DETAILS') ?></small>
    <span class="text-info">]</span>

    <ul class="bug-detail-list">
        <li>
            <span><?= L('BUG_HOW') ?> ？</span>
            <p><?= $bug->how ?></p>
        </li>
        <li>
            <span><?= L('BUG_WHAT') ?> ？</span>
            <p><?= $bug->what ?></p>
        </li>
        <li>
            <span><?= L('ERROR_MSG') ?></span>：
            <span><?= $bug->errmsg ?></span>
        </li>
        <li>
            <span><?= L('ERROR_CODE') ?></span>：
            <span><?= $bug->errcode ?></span>
        </li>
        <li>
            <span><?= L('OS') ?></span>：
            <span><?= $bug->os ?></span>
        </li>
        <li>
            <span><?= L('OS_VERSION') ?></span>：
            <span><?= $bug->os_ver ?></span>
        </li>
        <li>
            <span><?= L('PLATFORM') ?></span>：
            <span><?= $bug->platform ?></span>
        </li>
        <li>
            <span><?= L('PLATFORM_VERSION') ?></span>：
            <span><?= $bug->platform_ver ?></span>
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