<?php if (($pages = ($pages ?? 1)) > 1): ?>
<?php $page    = $_GET['page'] ?? 1; ?>
<?php $records = $records ?? 0; ?>
<?php $offset  = $offset ?? 16; ?>

<p class="pagination-bar">
    <button <?= ($page == 1) ? 'disabled' : '' ?>
    data-page="_start"><?= L('FIRST_PAGE') ?></button>

    <button <?= ($page >= $pages) ? 'disabled' : '' ?>
    data-page="_next"><?= L('NEXT_PAGE') ?></button>

    <button <?= ($page <= 1) ? 'disabled' : '' ?>
    data-page="_prior"><?= L('PRIOR_PAGE') ?></button>

    <button <?= ($page >= $pages) ? 'disabled' : '' ?>
    data-page="_end"><?= L('LAST_PAGE') ?></button>

    <input
    type="number"
    name="pagination-number"
    style="width:50px"
    placeholder="<?= L('INPUT_LEGAL_PAGE_NUMBER') ?>">

    <button <?= ($pages <= 1) ? 'disabled' : '' ?>
    name="goto-page"><?= L('GOTO') ?></button>

    <input type="hidden" name="records-count" value="<?= $records ?>">
    <input type="hidden" name="pagination-count" value="<?= $pages ?>">

    <p><i><code>(
        <?= L('PAGE_NOW', $page) ?>;
        <?= L('TOTAL_RECORDS', $records) ?>;
        <?= L('TOTAL_PAGES', $pages) ?>;
        <?= L('PAGE_SIZE', $offset) ?>
    )</code></i></p>
</p>
<?php endif ?>