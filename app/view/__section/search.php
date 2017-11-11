<?php $display = share_flush('hidden-search-bar') ? 'invisible-default' : ''?>

<span class="search-bar <?= $display ?>">
    <label>
        <input type="text" name="search"
        value="<?= ($keyword ?? '') ?>" required
        placeholder="<?= lang('PROVIDE_KEYWORDS') ?>">
    </label>

    <button name="search-btn">
        <?= lang('SEARCH') ?>
    </button>
    <button name="clear-search-btn">
        <?= lang('CLEAR') ?>
    </button>
    <button name="reset-all-btn">
        <?= lang('RESET_ALL') ?>
    </button>
</span>
