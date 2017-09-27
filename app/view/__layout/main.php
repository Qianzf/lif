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

<body>

    <?= $this->section('header') ?>

    <?= $__MAIN__ ?>

    <?= $this->section('footer') ?>

</body>
</html>
