<?php if ($display ?? true): ?>
<sup>
    <a href="<?= $route ?? '/' ?>">
        <button class="btn-shadow"><?= L('BACK_TO_LIST') ?></button>
    </a>
</sup>

<sup>
    <button
    onclick="back2last()"
    class="btn-shadow"><?= L('BACK_TO_LAST') ?></button>
</sup>
<?php endif ?>