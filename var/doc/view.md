For example:

- Layout example: main.php

``` php
<!DOCTYPE html>
<html>
<head>
<?= $this->css([
    'main'
]) ?>
</head>

<?= $this->js([
    'jquery.min'
]) ?>

<?= $this->section('header') ?>

<body>

    <?= $__MAIN__ ?>

</body>

<?= $this->section('footer') ?>

</html>
```

- Section example: footer.php

``` php
<footer>
    <em><?= sysmsg('SWITCH_LANG') ?></em>
    <select name="system-lang">
        
        <?php foreach ($languages as $key => $lang) { ?>
        <?php $selected = ($key == $sysLang) ? 'selected' : '' ?>
        <option value="<?= $key ?>" <?= $selected ?>><?= $lang ?></option>
        <?php } ?>

    </select>
</footer>

<?= $this->js([
    'main'
]) ?>
```

- Template example: index.php

``` php
<?= $this->layout('main') ?>
<?= $this->title([sysmsg('HOMEPAGE'), sysmsg('LIF')]) ?>
```

Well, if frontend developers don't familiar with PHP, the better way is to build web app via HTTP API.
