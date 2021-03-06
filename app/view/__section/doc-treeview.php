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
            <a href='<?=
            lrn("docs/folders/{$child->id}/edit?parent={$folder->id}")
            ?>'>
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

    <?php if ($unfoldables ?? false): ?>
    unfoldChildren(<?= json_encode($unfoldables) ?>)
    <?php endif ?>
})
function unfoldChildren(ids) {
    var folder = ids.pop()

    if (folder > 0) {
        unfoldChild(folder)
    }

    if (ids.length > 0) {
        setTimeout(function () {
            unfoldChildren(ids)
        }, 1500)
    }
}
function unfoldChild(id) {
    var folder = $('#treeview-folder-' + id)
    var query  = '?parent=<?= $folder->id ?>'

    if (folder.length < 1) {
        return false;
    }

    var parentLi = $(folder.parent()[0])
    var siblingDiv = $(folder.siblings()[0])

    var prefix  = "<?= config('app.route.prefix') ?>"
    if ('fold' == folder.data('status')) {
        return true
    }
    asyncr(prefix + '/docs/folders/' + id + '/unfold').then(function (res) {
        res.json().then(function (ret) {
            var html = ''
            if (ret.dat.docs) {
                ret.dat.docs.forEach(function (val, key) {
                    var selected = (val.id == '<?= $doc->id ?? -1 ?>')
                    ? 'style="color:darkcyan"' : '';

                    html += `
                    <li ${selected} onclick="reloadUseQuery('doc', ${val.id})">
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
                            <a href="${prefix}/docs/folders/${val.id}/edit${query}">
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

            folder.show()

            parentLi.removeAttr('class')
            parentLi.attr('class', 'closed collapsable lastCollapsable')

            siblingDiv.removeAttr('class')
            siblingDiv.attr('class', 'hitarea closed-hitarea collapsable-hitarea lastCollapsable-hitarea')

            folder.data('status', 'fold')
        })
    })
}
</script>