<?= $this->layout('main') ?>
<?= $this->title([L('VIEW_DOC_FOLDER'), L('LDTDFMS')]) ?>
<?= $this->section('common') ?>

<h2>
    <sub><code><?= "F{$folder->id}" ?></code></sub>
    <big><?= $folder->title ?></big>

    <?= $this->section('back_to', [
        'route' => '/dep/docs',
    ]) ?>

    <a href="/dep/docs/folders/<?= $folder->id ?>/edit">
        <button><?= L('EDIT') ?></button>
    </a>

    <a href="/dep/docs/new?folder=<?= $folder->id ?>">
        <button><?= L('ADD_DOC') ?></button>
    </a>

    <a href="/dep/docs/folders/new?parent=<?= $folder->id ?>">
        <button><?= L('ADD_CATE') ?></button>
    </a>
</h2>

<div class="doc-show-container">
    <div class="doc-show-folder">
        <dl class="doc-menu-tree">
        <?php if (isset($docs) && iteratable($docs)): ?>
        <?php foreach ($docs as $_doc): ?>
        <ol
        <?php if ($doc->id == $_doc->id): ?>
        class="doc-menu-title-selected"
        <?php endif ?>
        onclick="reloadUseQuery('doc', <?= $_doc->id ?>)">
            <?= $_doc->title ?>
        </ol>
        <?php endforeach ?>
        <?php endif ?>

        <?php if (isset($children) && iteratable($children)): ?>
        <?php foreach ($children as $child): ?>
        <li onclick="unfoldChild(this, <?= $child->id ?>)">
            <?= $child->title ?>
        </li>
        <?php endforeach ?>
        <?php endif ?>
        </dl>
    </div>

    <div class="doc-show-content">
        <?= $this->section('doc', [
            'display' => false,
        ]) ?>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('.doc-menu-tree dd, li, ol').bind({
            mouseover: function () {
                var item = $(this)
                if (! item.hasClass('doc-menu-title-selected')) {
                    item.removeClass('doc-menu-title-out')
                    item.addClass('doc-menu-title-over')
                }
            },
            mouseout: function () {
                var item = $(this)
                if (! $(this).hasClass('doc-menu-title-selected')) {
                    item.removeClass('doc-menu-title-over')
                    item.addClass('doc-menu-title-out')
                }
            }
        })
    })
    function unfoldChild(obj, id) {
        console.log($(obj))
        var headers = new Headers();
        headers.append('Access-Control-Allow-Origin', '*');
        headers.append('AUTHORIZATION', '');
        var init = {
            method: 'GET',
            credentials: 'include',
            headers: headers
        };
        var request = new Request('/dep/docs/folders/' + id + '/unfold', init);
        fetch(request).then(function (res) {
            res.json().then(function (ret) {
                console.log(ret.dat)
            });
        });
    }
</script>