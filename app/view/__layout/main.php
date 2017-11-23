<!DOCTYPE html>
<html>
<head>
    <title>
        <?= ($title ?? null) ?>
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

    <?= ($__MAIN__ ?? null) ?>

    <?= $this->section('footer') ?>

</body>
</html>
