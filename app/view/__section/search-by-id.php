<?php $searchingID = $_GET['id'] ?? false; ?>
<div id="search-by-id">
    <input
    <?php if (ispint($searchingID, false)): ?>
    value="<?= $searchingID ?>"
    <?php endif ?>
    type="number" name="id">
    <button name="search"><?= L('SEARCH_ID') ?></button>
    <?php if (! empty_safe($searchingID)): ?>
    <button name="cancel"><?= L('CANCEL') ?></button>
    <?php endif ?>
</div>