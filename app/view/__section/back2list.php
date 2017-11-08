<?php $action = lang(($model->id ? 'EDIT' : 'ADD')."_{$key}") ?>

<?= $this->title([
        $action,
        lang('LDTDFMS')
    ])
?>

<h4>
    <?= $action ?>
    <sup>
        <small>
            <a href="<?= $route ?>"><?= lang('BACK_TO_LIST') ?></a>
        </small>
    </sup>
</h4>
