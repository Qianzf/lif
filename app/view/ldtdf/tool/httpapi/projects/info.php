<?= $this->layout('main') ?>
<?= $this->title('HTTP API '.ldtdf('PROJECT')) ?>
<?= $this->section('common') ?>

<h2>
    <sub><code><?= "#{$project->id}" ?></code></sub>
    <big><?= $project->name ?></big>

    <?= $this->section('back_to', [
        'route' => lrn('tool/httpapi'),
    ]) ?>

    <a href='<?= lrn("tool/httpapi/projects/{$project->id}/edit") ?>'>
        <button><?= L('EDIT') ?></button>
    </a>

    <a href='<?= lrn("tool/httpapi/new?project={$project->id}") ?>'>
        <button><?= L('ADD_API') ?></button>
    </a>

    <a href='<?= lrn("tool/httpapi/cates/new?project={$project->id}") ?>'>
        <button><?= L('ADD_CATE') ?></button>
    </a>

    <a href='<?= lrn("tool/httpapi/env/new?project={$project->id}") ?>'>
        <button><?= L('ADD_ENV') ?></button>
    </a>
</h2>

<pre><code id="json" class="language-json"></code></pre>

<?= $this->js([
    'js/http.min',
    'js/vkbeautify',
]) ?>

<script type="text/javascript">
    lil.http.get('http://api.hcm.docker/api/members/current', {

    }, function (err, res) {
        console.log(err)
        
        $('#json').html(vkbeautify.json(JSON.stringify(err), 4))

        console.log(res)
    })
</script>
