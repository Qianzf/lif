<div id="search-by-id">
    <input
    <?php if (ispint($searchID = ($_GET['id'] ?? false), false)): ?>
    value="<?= $searchID ?>"
    <?php endif ?>
    type="number" name="id">
    <button name="search"><?= L('SEARCH_ID') ?></button>
    <button name="cancel"><?= L('CANCEL') ?></button>
</div>