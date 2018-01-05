$(window).ready(function () {
    $('select[name="system-lang"]').change(function () {
        reloadWithQuerys('lang', this.value)
    })
    $('select[name="loggedin"]').change(function () {
        let aTag = getATag(location.href)
        let url  = aTag.scheme + aTag.hostname + aTag.pathname
        window.location.href = '/dep/users/'
        + this.value
        + window.location.search
    })
    $('select[name="system-roles"]').change(function () {
        reloadWithQuerys('role', this.value)
    })
    $('button[name="search-btn"]').click(function () {
        search()
    })
    $('input[name="search"]').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault()
            search()
        }
    })
    $('button[name="clear-search-btn"]').click(function () {
        $('input[name="search"]').val('')
    })
    $('button[name="reset-all-btn"]').click(function () {
        $('input[name="search"]').val('')
        if (window.location.search) {
            let aTag = getATag(window.location.href)
            let url  = aTag.scheme + aTag.hostname + aTag.pathname

            window.location = url
        }
    })
    $('.pagination-bar button').click(function () {
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
    $('input[name="pagination-number"]').on('input', function () {
        let goTo = parseInt(this.value)
        let pageCount = parseInt($('input[name="pagination-count"]').val())
        if ((1 <= goTo) && (goTo <= pageCount)) {
            $('input[name="goto-page"]').attr('disabled', false)
            this.style.color = 'green'
            $(this).keydown(function (e) {
                if (e.which == 13) {
                    e.preventDefault()
                    tryReloadWithNewPage(this.value)
                }
            })
        } else {
            if (! isNaN(goTo)) {
                $('input[name="goto-page"]').attr('disabled', true)
                this.style.color = 'red'
            }
        }
    })
    $('#search-by-id input').on('input', function () {
        let id = parseInt(this.value)
        if (id > 0) {
            this.style.color = 'green'
            $(this).keydown(function (e) {
                if (e.which == 13) {
                    e.preventDefault()
                    reloadUseQuery('id', id)
                }
            })
        } else {
            this.style.color = 'red'
        }
    })
    $('#search-by-id button[name="search"]').on('click', function () {
        let id = parseInt($('#search-by-id input').val())
        if (id > 0) {
            reloadUseQuery('id', id)
        }
    })
    $('#search-by-id button[name="cancel"]').click(function () {
        $('#search-by-id input').val('')
        if (window.location.search) {
            let aTag = getATag(window.location.href)
            let url  = aTag.scheme + aTag.hostname + aTag.pathname

            window.location = url
        }
    })
    $('.filter-name-by-value-select').change(function () {
        reloadWithQuerys(this.name, this.value)
    })
    $('.query-filters').change(function () {
        if (this.value) {
            reloadWithQuerys(this.name, this.value)
        }
    })

    $('input[name="custom"]').click(function () {
        let outer  = $('.outer-task-detail')
        let custom = $('.custom-task-attr')
        let show = outer
        let hide = custom
        if ('yes' == this.value) {
            tryDisplayEditormd()
            show = custom
            hide = outer
        }

        hide.hide()
        hide.attr('disabled', true)
        show.removeClass('invisible-default')
        show.attr('disabled', false)
        show.show()

        removeRequired()
    })

    $('.time-sort').on({
        mouseover: function () {
            $(this).addClass('pointer')
        },
        click: function () {
            let sort = ('desc' == this.dataset.sort)
            ? 'asc' : 'desc'

            reloadWithQuerys('sort', sort)
        }
    })

    hasErrorOrNot()
})
function removeRequired() {
    let reference = $('.outer-task-detail')
    let custom = $('.custom-task-attr')
    if ('yes' == $('input[name="custom"]').val()) {
        reference.children('.required').removeAttr('required')
        custom.children('.required').attr('required', true)
    } else {
        custom.children('.required').removeAttr('required')
        reference.children('.required').attr('required', true)
    }
}
function tryDisplayEditormd()
{
    if (typeof EditorMDObjects != 'undefined') {
        for (let i in EditorMDObjects) {
            let height = EditorMDObjects[i].height
            ? EditorMDObjects[i].height
            : 300
            let width = EditorMDObjects[i].width
            ? EditorMDObjects[i].width
            : '80%'
            let editor = editormd(EditorMDObjects[i].id, {
                width   : width,
                height  : height,
                syncScrolling : 'single',
                path    : '/assets/editor.md/lib/',
                placeholder : EditorMDObjects[i].placeholder,
                emoji : true,
                // saveHTMLToTextarea : true,
                // previewTheme: 'github',
                // imageUpload : true,
                // imageFormats : ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'],
                // imageUploadURL : '/uploadfile',
                toolbarIcons: function () {
                    return [
                        'undo',
                        'redo',
                        'clear',
                        '|',
                        'bold',
                        'del',
                        'italic',
                        'quote',
                        'ucwords',
                        'uppercase',
                        'lowercase',
                        '|',
                        'h1',
                        'h2',
                        'h3',
                        'h4',
                        'h5',
                        'h6',
                        '|',
                        'list-ul',
                        'list-ol',
                        'hr',
                        '|',
                        'qiniu',
                        'link',
                        'reference-link',
                        'image',
                        '|',
                        'code',
                        'preformatted-text',
                        'code-block',
                        'table',
                        'datetime',
                        'emoji',
                        'html-entities',
                        'pagebreak',
                        'goto-line',
                        '|',
                        'watch',
                        'preview',
                        'fullscreen',
                        '|',
                        'search',
                        'help',
                        'info',
                    ];
                },
                toolbarIconsClass : {
                    qiniu : 'fa-cloud-upload'
                },
                lang : {
                    toolbar : {
                        qiniu : "自定义七牛上传",
                    }
                },
                toolbarHandlers : {
                  qiniu : function(cm, icon, cursor, selection) {
                      this.qiniuDialog()
                  }
                },
                qiniu : {
                    enable: true,
                    token_api : '/dep/tool/uploads/uptoken?raw=true',
                    public_domain : 'http://assets.hcmchi.com/',
                    upload_url : 'http://upload-z2.qiniu.com/',
                },
            })
        }
    }
}
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

                page = ((1 < currentPage) && (currentPage <= pageCount))
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
        reloadWithQuerys('page', page)
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
        reloadWithQuerys('search', search)
    }
}
function hasErrorOrNot() {
    let error = $('input[name="__error"]').val()
    if (error) {
        alert(error)
    }

    needBack2Last()
}
function needBack2Last() {
    let back2last = $('input[name="__back2last"]')
    let last = back2last.val()
    if (back2last && last) {
        back2last.val('')
        window.location.href = last
    }    
}
function back2last() {
    // window.history.back
    window.history.go(-1)

    return false
}
function getHost() {
    let aTag = getATag(window.location.href)
    return (aTag.scheme + aTag.hostname + aTag.pathname)
}
function reload(queryString) {
    let url = getHost()
    if (('undefined' != (typeof queryString)) && queryString) {
        url += ('?' + queryString)
    }

    window.location = url
}
function reloadUseQuery(key, val) {
     reload(key + '=' + val)
}
function reloadWithQuerys(key, val) {
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

    reload(newQueryString)
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
function asyncr(uri, type, auth, json) {
    type = type ? type : 'GET'
    auth = auth ? auth : ''

    let headers = new Headers()
    headers.append('Access-Control-Allow-Origin', '*')
    headers.append('AUTHORIZATION', auth)
    headers.append('X-REQUESTED-WITH', 'XMLHTTPREQUEST')

    return fetch(
        new Request(uri, {
            method: type,
            credentials: 'include',
            headers: headers
        })
    )
}