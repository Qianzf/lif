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

    hasErrorOrNot()
})
function hasErrorOrNot() {
    let error = $('input[name="__error"]').val()
    if (error) {
        alert(error)
    }
}
function reloadWithQuery(key, val)
{
    let queryString = window.location.search
    .replace('?', '')
    .split('&')

    let newQueryString = updateQueryString(
        queryString,
        key,
        val
    )
    let aTag = getATag(window.location.href)
    let url  = aTag.scheme + aTag.hostname + aTag.pathname

    window.location = url + '?' + newQueryString
}
function updateQueryString(queryString, key, val)
{
    let newQueryString = []
    let hasBefore = false

    if (queryString) {
        for (let i in queryString) {
            console.log(i, queryString)
            let pair = queryString[i].split('=')
            if (key == pair[0]) {
                hasBefore = true
                pair[1] = val
            }
            let newPair = pair.join('=')
            newQueryString.push(newPair)
        }
    }

    newQueryString = newQueryString.join('&')

    if (! hasBefore) {
        newQueryString += (key + '=' + val)
    }

    return newQueryString
}
function getATag(url)
{
    let a = document.createElement('a')
    let urlArr = url.split('/')

    scheme = urlArr[0] || 'http:'

    a.href = url
    a.scheme = scheme + '//'

    return a
}
