<h2>
    <sub><code><?= "D{$doc->id}" ?></code></sub>
    <big><?= $doc->title ?></big>

    <?= $this->section('back_to', [
        'route' => '/dep/docs',
    ]) ?>

    <a href="/dep/docs/<?= $doc->id ?>/edit">
        <button><?= L('EDIT') ?></button>
    </a>
</h2>

<div id="doc-content">
    <textarea
    id="doc-content-md"
    style="display:none"><?= $doc->content ?></textarea>
</div>

<?= $this->section('lib/editormd') ?>
<script type="text/javascript">
    $(function() {
        editormd.markdownToHTML("doc-content", {
            markdown        : $('#doc-content-md').val(),
            htmlDecode      : "style,script,iframe",
            tocm            : true,    // Using [TOCM]
            markdownSourceCode : true,
        })
    })
</script>