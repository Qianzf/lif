<!DOCTYPE html>
<html>
<head>
    <title>
        <?= $title ?>
    </title>
</head>

<?= $this->js([
    '/js/jquery.min'
]) ?>

<?= $this->section('header') ?>

<body>

    <?= $__main__ ?>

</body>

<?= $this->section('footer') ?>

</html>
