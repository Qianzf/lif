<?php $display = share_flush('hide-search-bar') ? 'invisible-default' : ''?>

<span class="search-bar <?= $display ?>">
    <label>
        <input type="text" name="search"
        value="<?= ($keyword ?? '') ?>" required
        placeholder="<?= lang('PROVIDE_KEYWORDS') ?>">
    </label>
    <button name="clear-search-btn">
        <?= lang('CLEAR') ?>
    </button>
    <button name="reset-all-btn">
        <?= lang('RESET_ALL') ?>
    </button>
</span>
