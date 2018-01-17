<label>
    <span class="label-title">
        <?= L('PRINCIPALS') ?>
        <sub><small>(<?= L('OPTIONAL') ?>)</small></sub>
    </span>

    <?php if (isset($developers) && iteratable($developers)): ?>
    <div class="checkbox-list">
    <?php foreach ($developers as $dev): ?>
    <?php $checked = in_array(
            ($did = ($dev['id'] ?? 0)),
            ($principals ?? [])
        ) ? 'checked' : '';
    ?>
        <label>
            <input
            <?= $checked ?>
            type="checkbox"
            value="<?= $did ?>"
            name="developers[]">
            <small>
            <span class="pointer">
                <span><?= $dev['name'] ?? L('UNKNOWN') ?></span>
                <sup><code>
                    <?= $dev['ability'] ?: L("ROLE_{$dev['role']}") ?>
                </code></sup>
            </span>
            </small>
        </label>
    <?php endforeach ?>
    </div>
    <?php endif ?>
</label>