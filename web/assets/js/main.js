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
        if (window.location.search) {
            let aTag = getATag(window.location.href)
            let url  = aTag.scheme + aTag.hostname + aTag.pathname

            window.location = url
        }
    })

    hasErrorOrNot()
})
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
