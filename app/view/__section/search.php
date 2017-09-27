<p>
    <input type="button" name="clear-search-btn"
    value="<?= lang('CLEAR') ?>">

    <input type="button" name="reset-all-btn"
    value="<?= lang('RESET_ALL') ?>">
    
    <input type="text" name="search"
    value="<?= ($keyword ?? '') ?>" required
    placeholder="<?= lang('PROVIDE_KEYWORDS') ?>">

    <input type="button" name="search-btn"
    value="<?= lang('SEARCH') ?>">
</p>
