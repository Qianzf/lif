<select name="system-roles">
    <option value="all"><?= lang('ALL_ROLES') ?></option>
    <?php if ($roles = share('system-roles')) { ?>
    <?php foreach ($roles as $role) { ?>
    <?php $selected = ($searchrole == $role) ? 'selected' : '' ?>
    <option value="<?= $role ?>" <?= $selected ?>>
        <?= lang($role) ?>
    </option>
    <?php } ?>
    <?php } ?>
</select>
