<?php if (isset($model) && $model->alive()): ?>
    <?php $object = $object ?? classname($model); ?>
    <div>
        <small>
            <span class="stub-2"></span>
            <span class="text-info">[</span>
            <?= L("{$object}_TRENDING") ?>
            <?php $symbol = (
                ($sort = ($_GET['trending'] ?? 'desc'))
                && (ci_equal($sort, 'desc'))
                ) ? '&#x25BC;' : '&#x25B2;';
            ?>
            <button
            onclick="resortTrending('<?= $sort ?>')"><?= $symbol ?></button>
            <span class="text-info">]</span>
        </small>

        <?= $this->section('trendings', [
            'displayRefType'  => $displayRefType  ?? true,
            'displayRefState' => $displayRefState ?? true,
            'displayComments' => $displayComments ?? true,
        ]) ?>
    </div>

    <script type="text/javascript">
        function resortTrending(sort) {
            let _sort = ('desc' == sort) ? 'asc' : 'desc'
            reloadWithQuerys('trending', _sort)
        }
    </script>
<?php endif ?>