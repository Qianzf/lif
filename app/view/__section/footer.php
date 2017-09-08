<footer>
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
