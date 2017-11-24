<?php if (isset($model) && $model->isAlive()): ?>
    <p>
        <span class="stub-2"></span>
        <span class="text-info">[</span>
        <small><?= lang('TASK_TRENDING') ?></small>
        <?php $sort = (isset($_GET['trending']) && in_array($_GET['trending'], [
            'asc', 'desc',
            ])) ? $_GET['trending'] : 'asc';
        ?>
        <button
        onclick="resortTrending('<?= $sort ?>')"
        class="fa fa-sort-<?= $sort ?>"></button>
        <span class="text-info">]</span>
        <?= $this->section('trendings') ?>
    </p>

    <script type="text/javascript">
        function resortTrending(sort) {
            let _sort = ('desc' == sort) ? 'asc' : 'desc'
            reloadWithQuerys('trending', _sort)
        }
    </script>
<?php endif ?>