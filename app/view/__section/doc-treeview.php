<?= $this->js('treeview/jquery.treeview') ?>
<?= $this->css('treeview/jquery.treeview') ?>

<h6>
    <span class="stub"></span>
    <i><?= L('CATE_TREE') ?></i>
</h6>

<ul id="treeview-container" class="filetree treeview-famfamfam treeview">
    <?php if (isset($docs) && iteratable($docs)): ?>
    <?php foreach ($docs as $_doc): ?>
    <li
    <?php if ($_doc->id == $doc->id): ?>
    style="color:darkcyan"
    <?php endif ?>
    onclick="reloadUseQuery('doc', <?= $_doc->id ?>)">
        <span class="file"><?= $_doc->title ?></span>
    </li>
    <?php endforeach ?>
    <?php endif ?>

    <?php if (isset($children) && iteratable($children)): ?>
    <?php foreach ($children as $child): ?>
    <li class="closed expandable" onclick="unfoldChild(<?= $child->id ?>)">
        <div class="hitarea collapsable-hitarea lastCollapsable-hitarea"></div><span class="folder">
            <?= $child->title ?>
            <sup>
            <a href='<?= lrn("docs/folders/{$child->id}/edit") ?>'>
                <button><i><?= L('EDIT') ?></i></button>
            </a>
            </sup>
        </span>
        <ul id="treeview-folder-<?= $child->id ?>"></ul>
    </li>
    <?php endforeach ?>
    <?php endif ?>
</ul>

<?= $this->js('treeview/jquery.treeview') ?>
<?= $this->css('treeview/jquery.treeview') ?>
<script type="text/javascript">
$(document).ready(function(){
    $("#treeview-container").treeview({
        // toggle: function() {
        //     console.log("%s was toggled.", $(this).find(">span").text())
        // }
    })
})
function unfoldChild(id) {
    var folder  = $('#treeview-folder-' + id)
    var prefix  = "<?= config('app.route.prefix') ?>"
    if ('fold' == folder.data('status')) {
        return true
    }
    asyncr(prefix + '/docs/folders/' + id + '/unfold').then(function (res) {
        res.json().then(function (ret) {
            var html = ''
            if (ret.dat.docs) {
                ret.dat.docs.forEach(function (val, key) {
                    html += `
                    <li onclick="reloadUseQuery('doc', ${val.id})">
                        <span class="file">${val.title}</span>
                    </li>
                    `
                })
            }
            if (ret.dat.children) {
                ret.dat.children.forEach(function (val, key) {
                    html += `
                    <li class="closed expandable" onclick="unfoldChild(${val.id})">
                        <div class="hitarea collapsable-hitarea"></div>
                        <span class="folder">
                            ${val.title}
                            <sup>
                            <a href="${prefix}/docs/folders/${val.id}/edit">
                                <button><?= L('EDIT') ?></button>
                            </a>
                            </sup>
                        </span>
                        <ul id="treeview-folder-${val.id}"></ul>
                    </li>`
                })
            }

            if (html) {
                folder
                .append(html)
                .treeview({
                    add: html
                })
            }

            folder.data('status', 'fold')
        })
    })
}
</script>