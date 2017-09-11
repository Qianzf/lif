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

    <?= $__MAIN__ ?>

</body>

<?= $this->section('footer') ?>

</html>
