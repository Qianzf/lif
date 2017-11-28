<?php $width = $width ?? '200'; ?>
<?php $api   = $api   ?? 'api'; ?>
<?php $sresKey = $sresKey ?? 'id'; ?>
<?php $sresVal = $sresVal ?? 'name'; ?>
<?php $sresKeyInput = $sresKeyInput ?? 'name'; ?>

<div style="width:<?= $width?>px;display:inline-block;">
    <div id="selected-search-res">
        <label
        class="fa fa-angle-down"
        onclick="startInstantSearch(this)"
        data-angle="down"></label>
        <span></span>
    </div>

    <div id="instant-search-and-show">
        <input type="text"
        id="instant-search-bar"
        onclick="instantSearch()"
        onkeyup="instantSearch()"
        placeholder="<?= lang('PROVIDE_KEYWORDS') ?>">
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
            instantSearchAndShow.show()
            selectedSearchResult.removeClass('fa-angle-down')
            selectedSearchResult.addClass('fa-angle-up')
        }
    }
    function instantSearch() {
        $('#instant-search-res-list').show()
        let search = $('#instant-search-bar').val()
        $.get('<?= $api ?>', {
            'search': search
        }, function (res) {
            let results = res.dat ? '' : '<?= lang('NO_RESULT')?>'
            for (let i in res.dat) {
                results += '<li onclick="chooseSearchResult(this)" data-key="'
                + res.dat[i].<?= $sresKey ?>
                + '" data-value="'
                + res.dat[i].<?= $sresVal ?>
                + '">'
                + res.dat[i].<?= $sresVal ?>
                + '</li>'
            }

            $('#instant-search-res-list').html(results)
        })
    }
    function chooseSearchResult(result) {
        $('input[name="<?= $sresKeyInput ?>"]').val(result.dataset.key)
        let selectedSearchResult = $('#selected-search-res label')
        selectedSearchResult.removeClass('fa-angle-up')
        selectedSearchResult.addClass('fa-angle-down')
        selectedSearchResult.data().angle = 'down'
        $('#instant-search-and-show').hide()
        let html = result.dataset.value
        + '<span class="search-res-right-close"><i class="fa fa-remove" onclick="removeSelectedResult()"></i></span>'

        $('#selected-search-res span').html(html)
    }
    function removeSelectedResult() {
        $('#selected-search-res span').html('')
        startInstantSearch()
    }
</script>