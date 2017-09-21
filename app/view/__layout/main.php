<!DOCTYPE html>
<html>
<head>
    <title>
        <?= $title ?>
    </title>

    <?=
        css([
            'css/main',
        ]);

        js([
            'js/jquery.min',
        ]);
    ?>

</head>

<br>

<?= $this->section('header') ?>

<br>

<body>

    <?= $__MAIN__ ?>

</body>

<br>

<?= $this->section('footer') ?>

<br>

</html>
