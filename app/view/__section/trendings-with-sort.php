<?php if (isset($model) && $model->isAlive()): ?>
    <?php $object = $object ?? classname($model); ?>
    <div>
        <small>
            <span class="stub-2"></span>
            <span class="text-info">[</span>
            <?= L("{$object}_TRENDING") ?>
            <?php $sort = (isset($_GET['trending']) && in_array($_GET['trending'], [
                'asc', 'desc',
                ])) ? $_GET['trending'] : 'desc';
            ?>
            <button
            onclick="resortTrending('<?= $sort ?>')"
            class="fa fa-sort-<?= $sort ?>"></button>
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