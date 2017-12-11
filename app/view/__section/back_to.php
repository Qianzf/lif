<?php if ($display ?? true): ?>
<sup>
    <a href="<?= $route ?? '/' ?>">
        <button class="btn-shadow"><?= L('BACK_TO_LIST') ?></button>
    </a>
</sup>

<sup>
    <a href="<?= share('url_previous') ?>">
        <button class="btn-shadow"><?= L('BACK_TO_LAST') ?></button>
    </a>
</sup>
<?php endif ?>