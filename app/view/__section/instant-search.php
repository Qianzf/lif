<?php $width   = $width ?? '200'; ?>
<?php $api     = $api   ?? 'api'; ?>
<?php $oldVal  = $oldVal  ?? false; ?>
<?php $sresKey = $sresKey ?? 'id'; ?>
<?php $sresVal = $sresVal ?? 'name'; ?>
<?php $sresKeyInput = $sresKeyInput ?? 'name'; ?>
<?php $closeSelectedHTML = '<span class="search-res-right-close"><i class="fa fa-remove" onclick="removeSelectedResult()"></i></span>';
?>

<div style="width:<?= $width?>px;display:inline-block;">
    <div id="selected-search-res">
        <label
        class="fa fa-angle-down"
        onclick="startInstantSearch(this)"
        data-angle="down"></label>
        <span>
            <?php if ($oldVal): ?>
                <?= $oldVal, $closeSelectedHTML ?>
            <?php endif ?>
        </span>
    </div>

    <div id="instant-search-and-show">
        <input type="text"
        id="instant-search-bar"
        onkeyup="instantSearch()"
        placeholder="<?= L('PROVIDE_KEYWORDS') ?>">
        <ul id="instant-search-res-list"></ul>
    </div>
</div>

<script type="text/javascript">
    function startInstantSearch(obj) {
        let instantSearchAndShow = $('#instant-search-and-show')
        let selectedSearchResult = $('#selected-search-res label')
        obj = (typeof obj == 'undefined')
        ? selectedSearchResult
        : $(obj)

        if (obj.data().angle == 'up') {
            obj.data().angle = 'down'
            instantSearchAndShow.hide()
            selectedSearchResult.removeClass('fa-angle-up')
            selectedSearchResult.addClass('fa-angle-down')
        } else {
            obj.data().angle = 'up'
            instantSearch()
            instantSearchAndShow.show()
            selectedSearchResult.removeClass('fa-angle-down')
            selectedSearchResult.addClass('fa-angle-up')
        }
    }
    function instantSearch() {
        $('#instant-search-res-list').show()
        searchKeywords($('#instant-search-bar').val())
    }
    function searchKeywords(search) {
        $.get('<?= $api ?>', {
            'search': search
        }, function (res) {
            let results = res.dat
            ? '' : '<small><i><?= L('NO_RESULT') ?></i></small>'

            let height  = 50
            for (let i in res.dat) {
                results += '<li onclick="chooseSearchResult(this)" data-key="'
                + res.dat[i].<?= $sresKey ?>
                + '" data-value="'
                + res.dat[i].<?= $sresVal ?>
                + '">'
                + res.dat[i].<?= $sresVal ?>
                + '</li>'
                height += 50
            }

            let instantSearchResList = $('#instant-search-res-list')
            instantSearchResList.css('height', height)
            instantSearchResList.html(results)
        })
    }
    function chooseSearchResult(result) {
        $('input[name="<?= $sresKeyInput ?>"]').val(result.dataset.key)
        let selectedSearchResult = $('#selected-search-res label')
        selectedSearchResult.removeClass('fa-angle-up')
        selectedSearchResult.addClass('fa-angle-down')
        selectedSearchResult.data().angle = 'down'
        $('#instant-search-and-show').hide()
        let html = result.dataset.value + '<?= $closeSelectedHTML ?>'
        $('#selected-search-res span').html(html)
    }
    function removeSelectedResult() {
        removeAllSelectedResult()
        startInstantSearch()
    }
</script>