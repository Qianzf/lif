<!DOCTYPE html>
<html>
<head>
    <title>
        <?= $title ?>
    </title>

    <?= js([
        'js/jquery.min'
    ]) ?>
</head>

<body>

    <?= $__MAIN__ ?>

</body>

<?= $this->section('footer') ?>

</html>
