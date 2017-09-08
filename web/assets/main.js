$(window).ready(function () {
    $('select[name="system-lang"]').change(function () {
        reloadWithQuery('lang', this.value)
    })
})

function reloadWithQuery(key, val)
{
    let queryString = window.location.search.replace('?', '').split('&')

    if (queryString) {
        let newQueryString = updateQueryString(
            queryString,
            key,
            val
        )
        let aTag = getATag(window.location.href)
        let url  = aTag.scheme + aTag.hostname + aTag.pathname

        window.location = url + '?' + newQueryString
    }
}
function updateQueryString(queryString, key, val)
{
    let newQueryString = []

    for (i in queryString) {
        let pair = queryString[i].split('=')
        if (key == pair[0]) {
            pair[1] = val
        }
        let newPair = pair.join('=')
        newQueryString.push(newPair)
    }

    return newQueryString.join('&')
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
