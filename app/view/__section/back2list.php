<?php $action = $action ?? ($model->id ? 'EDIT' : 'ADD'); ?>
<?php $title  = L("{$action}_{$key}"); ?>

<?= $this->title([
        $title,
        L('LDTDFMS')
    ])
?>
<?= $this->section('common') ?>
<h4>
    <?= $title ?>
    <sup><small>[
        <a href="<?= $route ?>"><?= L('BACK_TO_LIST') ?></a>
    ]</small></sup>

    <sup><small>[
        <a href="<?= share('url_previous') ?>">
            <?= L('BACK_TO_LAST') ?>
        </a>
    ]</small></sup>
</h4>