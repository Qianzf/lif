For example:

- Layout example: main.php

``` php
<!DOCTYPE html>
<html>
<head>
<?=
    echo $this->css([
        'main'
    ]);

    // Or
    css([
        'main'
    ]);
?>
</head>

<?=
    echo $this->js([
    'jquery'
    ]);

    // Or
    js([
        'jquery'
    ]);
?>

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
<?= $this->title([lang('HOMEPAGE'), lang('LIF')]) ?>
```

Well, if frontend developers don't familiar with PHP, the better way is to build web app via HTTP API.

- PHP Raw code short cuts

Besides normal PHP `{}` blocks, we can also use `:` and `end***` grammers to do the same things like below:

```php
<?php if (isset($groups) && iteratable($groups)) : ?>
<?php foreach ($groups as $group): ?>
    <tr>
        <td><?= $group->name ?></td>
        <td>
            <button>
                <a href="/path/to/resources/<?= $group->id ?>">
                    <?= lang('EDIT') ?>
                </a>
            </button>
        </td>
    </tr>
<?php endforeach ?>
<?php endif ?>
```

- Share data between views

``` php
// app/view/index.php
<?php share('hidden-search-bar', true) ?>
<?= $this->layout('main') ?>
<?= $this->title([lang('HOMEPAGE'), lang('LiF')]) ?>
<?= $this->section('search') ?>
<!-- ... -->

// app/view/__section/search.php
<?php $display = share_flush('hidden-search-bar') ? 'invisible-default' : ''?>

<span class="search-bar <?= $display ?>">
<!-- ... -->
</span>
```
