<footer>
    <button class="btn-shadow" disabled><i>
        <?= sysmsg('SWITCH_LANG') ?>
    </i></button>

    <select name="system-lang">
    
        <?php foreach (share('languages') as $key => $lang) { ?>
        <?php $selected = ($key == syslang()) ? 'selected' : '' ?>
        <option value="<?= $key ?>" <?= $selected ?>><?= $lang ?></option>
        <?php } ?>

    </select>

    <span class="stub"></span>
    <sub><small><i>(
        <?= get_lif_ver() ?>
    )</small></i></sub>
</footer>

<input
type="hidden"
name="__error"
value="<?= lang(share_flush('__error')) ?>">

<?= js([
    'js/main'
]) ?>
