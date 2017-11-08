<?php $page    = $_GET['page'] ?? 1; ?>
<?php $records = $records ?? 0; ?>
<?php $pages   = $pages ?? 0; ?>
<?php $offset  = $offset ?? 16; ?>

<p class="pagination-bar">
    <input type="button" data-page="_start" value="<?= lang('FIRST_PAGE') ?>">
    <input type="button" data-page="_next" value="<?= lang('NEXT_PAGE') ?>">
    <input type="button" data-page="_prior" value="<?= lang('PRIOR_PAGE') ?>">
    <input type="button" data-page="_end" value="<?= lang('LAST_PAGE') ?>">
    <input type="number" name="pagination-number"
    placeholder="<?= lang('INPUT_PAGE_NUMBER') ?>">
    <input type="button" data-page="" value="<?= lang('GOTO') ?>">
    <input type="hidden" name="records-count" value="<?= $records ?>">
    <input type="hidden" name="pagination-count" value="<?= $pages ?>">

    <dl>
        <dd><code>
            <?= lang('PAGE_NOW', $page) ?>
        </code></dd>
        <dd><code>
            <?= lang('TOTAL_RECORDS', $records) ?>
        </code></dd>
        <dd><code>
            <?= lang('TOTAL_PAGES', $pages) ?>
        </code></dd>
        <dd><code>
            <?= lang('PAGE_SIZE', $offset) ?>
        </code></dd>
    </dl>
</p>
