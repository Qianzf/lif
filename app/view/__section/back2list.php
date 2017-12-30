<?php $mid    = $model->id ?? null; ?>
<?php $action = $action ?? ($mid ? 'EDIT' : 'ADD'); ?>
<?php $key    = $key ?? 'UNKNOWN'; ?>

<?= $this->title($title  = ldtdf("{$action}_{$key}")) ?>
<?= $this->section('common') ?>
<h4>
    <?= $title ?>
    
    <?= $this->section('back_to', [
        'route' => ($route ?? '/dep'),
    ]) ?>

    <?php if (isset($buttons) && iteratable($buttons)): ?>
    <?php foreach ($buttons as $button): ?>
    <?php if ($button['alive'] ?? false): ?>
    <a href="<?= $button['route'] ?>">
        <button><?= L($button['name']) ?></button>
    </a>
    <?php endif ?>
    <?php endforeach ?>
    <?php endif ?>
</h4>