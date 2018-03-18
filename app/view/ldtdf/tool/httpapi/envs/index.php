<?= $this->layout('main') ?>
<?= $this->title(ldtdf('HTTP API ENVS')) ?>
<?= $this->section('common') ?>

<dl>
    <dd>
        <a href="<?= lrn('/tool/httpapi/projects/'.$project->id.'/envs/new') ?>">
            <button><?= L('ADD_API_ENV') ?></button>
        </a>
    </dd>
</dl>

