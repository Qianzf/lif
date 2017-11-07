<?= $this->layout('main') ?>
<?= $this->title([
        lang(($project->id ? 'EDIT' : 'ADD').'_PROJECT'),
        lang('LDTDFMS')
    ])
?>

<br>

<form method="POST" autocomplete="off">
    <label>
        <?= lang('TITLE') ?>
        <input type="text" name="name" required value="<?= $project->name ?>">
    </label><br>

    <label>
        <?= 'URL' ?>
         <input type="text" name="url" required value="<?= $project->url ?>">
    </label><br>

    <label>
        <?= 'VCS' ?>
        <select name="vcs" required>
            <option value="git">git</option>
        </select>
    </label><br>

    <label>
        <?= lang('DESCRIPTION') ?>
        <textarea name="desc"><?= $project->desc ?></textarea>
    </label><br><br>

    <input type="submit"
    value="<?= $project->id ? lang('UPDATE') : lang('CREATE') ?>">

</form>

<br>
