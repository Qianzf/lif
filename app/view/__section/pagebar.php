<?php $page    = $_GET['page'] ?? 1; ?>
<?php $records = $records ?? 0; ?>
<?php $pages   = $pages ?? 1; ?>
<?php $offset  = $offset ?? 16; ?>

<p class="pagination-bar">
    <input <?= ($page == 1) ? 'disabled' : '' ?>
    type="button" data-page="_start" value="<?= L('FIRST_PAGE') ?>">

    <input <?= ($page >= $pages) ? 'disabled' : '' ?>
    type="button" data-page="_next" value="<?= L('NEXT_PAGE') ?>">

    <input <?= ($page <= 1) ? 'disabled' : '' ?>
    type="button" data-page="_prior" value="<?= L('PRIOR_PAGE') ?>">

    <input <?= ($page >= $pages) ? 'disabled' : '' ?>
    type="button" data-page="_end" value="<?= L('LAST_PAGE') ?>">

    <input type="number" name="pagination-number"
    placeholder="<?= L('INPUT_LEGAL_PAGE_NUMBER') ?>">

    <input <?= ($pages <= 1) ? 'disabled' : '' ?>
    type="button" name="goto-page" value="<?= L('GOTO') ?>">
    <input type="hidden" name="records-count" value="<?= $records ?>">
    <input type="hidden" name="pagination-count" value="<?= $pages ?>">

    <p><i><code>(
        <?= L('PAGE_NOW', $page) ?>;
        <?= L('TOTAL_RECORDS', $records) ?>;
        <?= L('TOTAL_PAGES', $pages) ?>;
        <?= L('PAGE_SIZE', $offset) ?>
    )</code></i></p>
</p>
