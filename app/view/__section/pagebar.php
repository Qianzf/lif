<?php $page    = $_GET['page'] ?? 1; ?>
<?php $records = $records ?? 0; ?>
<?php $pages   = $pages ?? 0; ?>
<?php $offset  = $offset ?? 16; ?>

<p class="pagination-bar">
    <input <?= ($page == 1) ? 'disabled' : '' ?>
    type="button" data-page="_start" value="<?= lang('FIRST_PAGE') ?>">

    <input <?= ($page >= $pages) ? 'disabled' : '' ?>
    type="button" data-page="_next" value="<?= lang('NEXT_PAGE') ?>">

    <input <?= ($page <= 1) ? 'disabled' : '' ?>
    type="button" data-page="_prior" value="<?= lang('PRIOR_PAGE') ?>">

    <input <?= ($page >= $pages) ? 'disabled' : '' ?>
    type="button" data-page="_end" value="<?= lang('LAST_PAGE') ?>">

    <input type="number" name="pagination-number"
    placeholder="<?= lang('INPUT_LEGAL_PAGE_NUMBER') ?>">

    <input <?= ($pages <= 1) ? 'disabled' : '' ?>
    type="button" name="goto-page" value="<?= lang('GOTO') ?>">
    <input type="hidden" name="records-count" value="<?= $records ?>">
    <input type="hidden" name="pagination-count" value="<?= $pages ?>">

    <p><i><code>(
        <?= lang('PAGE_NOW', $page) ?>;
        <?= lang('TOTAL_RECORDS', $records) ?>;
        <?= lang('TOTAL_PAGES', $pages) ?>;
        <?= lang('PAGE_SIZE', $offset) ?>
    )</code></i></p>
</p>
