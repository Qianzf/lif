<?= $this->layout('main') ?>
<?= $this->title([lang('TRENDING'), lang('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<?php if (isset($trendings) && iteratable($trendings)) : ?>
<?= $this->section('filter/user') ?>
<?= $this->section('trendings') ?>
<?= $this->section('pagebar') ?>
<?php endif ?>
