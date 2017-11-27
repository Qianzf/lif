<?php if (isset($model) && $model->isAlive()): ?>
    <?php $object = $object ?? classname($model); ?>
    <div>
        <h6>
            <span class="stub-2"></span>
            <span class="text-info">[</span>
            <small><?= lang("{$object}_TRENDING") ?></small>
            <?php $sort = (isset($_GET['trending']) && in_array($_GET['trending'], [
                'asc', 'desc',
                ])) ? $_GET['trending'] : 'asc';
            ?>
            <button
            onclick="resortTrending('<?= $sort ?>')"
            class="fa fa-sort-<?= $sort ?>"></button>
            <span class="text-info">]</span>
        </h6>

        <?= $this->section('trendings') ?>
    </div>

    <script type="text/javascript">
        function resortTrending(sort) {
            let _sort = ('desc' == sort) ? 'asc' : 'desc'
            reloadWithQuerys('trending', _sort)
        }
    </script>
<?php endif ?>