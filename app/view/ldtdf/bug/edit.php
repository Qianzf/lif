<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $bug,
    'key'   => 'BUG',
    'route' => '/dep/bugs',
]) ?>

<?php if (isset($bug) && is_object($bug)): ?>
<?php $bid = $bug->alive() ? $bug->id : 'new'; ?>

<form method="POST" action="/dep/bugs/<?= $bid ?>">
    <?= csrf_feild() ?>

    <label>
        <span class="label-title">* <?= L('TITLE') ?></span>
        <input
        type="text"
        name="title"
        required
        placeholder="<?= L('BUG_TITLE') ?>"
        value="<?= $bug->title ?>">
    </label>

    <label>
        <span class="label-title">* <?= L('OS') ?></span>
        <select name="os">
            <?php if (isset($oses) && iteratable($oses)): ?>
                <?php foreach ($oses as $os): ?>
                <option
                <?php if (strtolower($os) == strtolower($bug->os)): ?>
                selected
                <?php endif ?>><?= $os ?></option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
    </label>

    <label>
        <span class="label-title">* <?= L('OS_VER') ?></span>
        <input
        type="text"
        name="os_ver"
        required
        placeholder="<?= L('OS_VERSION_EG', 'macOS Sierra 10.12.6') ?>"
        value="<?= $bug->os_ver ?>">
    </label>

    <label>
        <span class="label-title">* <?= L('PLATFORM') ?></span>
        <input
        type="text"
        name="platform"
        required
        placeholder="<?= L('EG', L('CLIENT')), 'A / Chrome' ?>"
        value="<?= $bug->platform ?>">
    </label>

    <label>
        <span class="label-title">* <?= L('PLATFORM_VERSION') ?></span>
        <input
        type="text"
        name="platform_ver"
        required
        placeholder="<?= L('EG', L('CLIENT')) ?>A v1.1.2"
        value="<?= $bug->platform_ver ?>">
    </label>

    <label>
        <span class="label-title"><?= L('ERROR_MSG') ?></span>
        <input
        type="text"
        name="errmsg"
        placeholder="<?= L('EG'), ': ', L('SERVICE_BUSY') ?>"
        value="<?= $bug->errmsg ?>">
    </label>

    <label>
        <span class="label-title"><?= L('ERROR_CODE') ?></span>
        <input
        type="text"
        name="errcode"
        placeholder="<?= L('EG', '5001') ?>"
        value="<?= $bug->errcode ?>">
    </label>

    <label>
        <span class="label-title">* <?= L('BUG_HOW') ?></span>
        <textarea
        name="how"
        required
        placeholder="<?= L('BUG_HOW_STUB') ?>"><?= $bug->how ?></textarea>
    </label>

    <label>
        <span class="label-title"><?= L('RECURABLE') ?></span>
        <input type="radio" name="recurable" value="yes" checked>
        <span><?= L('YES') ?></span>
        <input type="radio" name="recurable" value="no">
        <span><?= L('NO') ?></span>
    </label>

    <label>
        <span class="label-title">* <?= L('BUG_WHAT') ?></span>
        <textarea
        name="what"
        required
        placeholder="<?= L('BUG_WHAT_STUB') ?>"
        ><?= $bug->what ?></textarea>
    </label>

    <label>
        <span class="label-title"><?= L('REMARKS') ?></span>
        <div
        id="bug-others"
        class="editormd editormd-vertical">
            <textarea
            name="extra"
            class="editormd-markdown-textarea"
            name="extra"><?= $bug->extra ?></textarea>
        </div>
    </label>

    <?php if ($editable ?? false) : ?>
    <?= $this->section('submit', [
        'model' => $bug
    ]) ?>
    <?php endif ?>
</form>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    var EditorMDObjects = [
    {
        id : 'bug-others',
        placeholder : "<?= L('NOTES'), ' / ', L('ATTACHMENT_ETC') ?>"
    }
    ]
    $(function() {
        tryDisplayEditormd()
    })
</script>
<?php endif ?>