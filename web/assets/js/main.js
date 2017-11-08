$(window).ready(function () {
    $('select[name="system-lang"]').change(function () {
        reloadWithQuery('lang', this.value)
    })
    $('select[name="loggedin"]').change(function () {
        let aTag = getATag(location.href)
        let url  = aTag.scheme + aTag.hostname + aTag.pathname
        window.location.href = '/dep/user/'
        + this.value
        + window.location.search
    })
    $('select[name="system-roles"]').change(function () {
        reloadWithQuery('role', this.value)
    })
    $('input[name="search-btn"]').click(function () {
        search()
    })
    $('input[name="search"]').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault()
            search()
        }
    })
    $('input[name="clear-search-btn"]').click(function () {
        $('input[name="search"]').val('')
    })
    $('input[name="reset-all-btn"]').click(function () {
        $('input[name="search"]').val('')
        if (window.location.search) {
            let aTag = getATag(window.location.href)
            let url  = aTag.scheme + aTag.hostname + aTag.pathname

            window.location = url
        }
    })
    $('.pagination-bar input[type="button"]').click(function () {
        let page   = 1
        let _page  = this.dataset.page
        let __page = $('input[name="pagination-number"]').val()

        if (_page) {
            page = _page
        } else if (__page) {
            page = __page
        }

        tryReloadWithNewPage(page)
    })
    $('input[name="pagination-number"]').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault()
            tryReloadWithNewPage(this.value)
        }
    })
    $('#env-types-filter').change(function () {
        reloadWithQuery('type', this.value)
    })

    $('input[name="custom"]').click(function () {
        if ('no' == this.value) {
            $('.custom-task-attr').hide();
            $('.outer-task-detail').show();
        } else {
            $('.custom-task-attr').show();
            $('.outer-task-detail').hide();
        }
    })

    hasErrorOrNot()
})
function tryReloadWithNewPage(page)
{
    let canReload = false

    if (!isNaN(parseInt(page)) && (page > 0)) {
        canReload = true
        page = parseInt(page)
    } else if (-1 != $.inArray(page, [
        '_start',
        '_next',
        '_prior',
        '_end',
    ])) {
        canReload = true
        let pageCount = $('input[name="pagination-count"]').val()
        pageCount = (!isNaN(parseInt(pageCount)) && (pageCount > 0))
        ? parseInt(pageCount)
        : -1
        switch (page) {
            case '_start' : {
                page = 1
            } break
            case '_next' : {
                let currentPage = getCurrentQueryPage()

                page = ((0 < currentPage) && (currentPage < pageCount))
                ? (currentPage + 1) : 1
            } break
            case '_prior' : {
                let currentPage = getCurrentQueryPage()

                page = ((1 < currentPage) && (currentPage < pageCount))
                ? (currentPage - 1) : 1
            } break
            case '_end' : {
                page = pageCount
            } break
            default : {
                canReload = false
            } break
        }
    }

    if (canReload) {
        reloadWithQuery('page', page)
    }
}
function getCurrentQueryPage() {
    let currentPage  = 1
    let queryStrings = window.location.search.replace('?', '').split('&')

    if (queryStrings) {
        for (let i in queryStrings) {
            let keyVal = queryStrings[i].split('=')
            if ('page' == keyVal[0]) {
                currentPage = (!isNaN(parseInt(keyVal[1])) && (keyVal[1] > 0))
                ? parseInt(keyVal[1]) : 1

                break
            }
        }
    }

    return currentPage
}
function search() {
    let search = $('input[name="search"]').val()
    if (search.length > 0) {
        reloadWithQuery('search', search)
    }
}
function hasErrorOrNot() {
    let error = $('input[name="__error"]').val()
    if (error) {
        alert(error)
    }
}
function reloadWithQuery(key, val) {
    let queryStringBefore = window.location.search

    if (queryStringBefore) {
        queryStringBefore = queryStringBefore
        .replace('?', '')
        .split('&')
    }

    let newQueryString = updatequeryStringBefore(
        queryStringBefore,
        key,
        val
    )

    let aTag = getATag(window.location.href)
    let url  = aTag.scheme + aTag.hostname + aTag.pathname

    window.location = url + '?' + newQueryString
}
function updatequeryStringBefore(queryStringBefore, key, val) {
    let newQueryString = []
    let noThisQueryKeyBefore = true

    if (queryStringBefore) {
        for (let i in queryStringBefore) {
            let pair = queryStringBefore[i].split('=')
            if (key == pair[0]) {
                noThisQueryKeyBefore = false
                pair[1] = val
            }
            let newPair = pair.join('=')
            newQueryString.push(newPair)
        }
    }

    if (noThisQueryKeyBefore) {
        newQueryString.push(key + '=' + val)
    }

    newQueryString = newQueryString.join('&')

    return newQueryString
}
function getATag(url) {
    let a = document.createElement('a')
    let urlArr = url.split('/')

    scheme = urlArr[0] || 'http:'

    a.href = url
    a.scheme = scheme + '//'

    return a
}
