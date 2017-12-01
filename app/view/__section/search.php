<?php $display = share_flush('hide-search-bar') ? 'invisible-default' : ''?>

<span class="search-bar <?= $display ?>">
    <label>
        <input type="text" name="search"
        value="<?= ($keyword ?? '') ?>" required
        placeholder="<?= L('PROVIDE_KEYWORDS') ?>">
    </label>
    <button name="clear-search-btn">
        <?= L('CLEAR') ?>
    </button>
    <button name="reset-all-btn">
        <?= L('RESET_ALL') ?>
    </button>
</span>
