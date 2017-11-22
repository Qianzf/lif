<?php $action = $action ?? ($model->id ? 'EDIT' : 'ADD'); ?>
<?php $title  = lang("{$action}_{$key}"); ?>

<?= $this->title([
        $title,
        lang('LDTDFMS')
    ])
?>
<?= $this->section('common') ?>
<h4>
    <?= $title ?>
    <sup>
        <small>
            <a href="<?= $route ?>"><?= lang('BACK_TO_LIST') ?></a>
        </small>
    </sup>
</h4>
