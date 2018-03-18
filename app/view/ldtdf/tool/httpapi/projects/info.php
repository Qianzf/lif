<?= $this->layout('main') ?>
<?= $this->title('HTTP API '.ldtdf('PROJECT')) ?>
<?= $this->section('common') ?>

<h2>
    <sub><code><?= "#{$project->id}" ?></code></sub>
    <big><?= $project->name ?></big>

    <?= $this->section('back_to', [
        'route' => lrn('tool/httpapi/projects'),
    ]) ?>

    <a href="<?= lrn("/tool/httpapi/projects/{$project->id}/edit") ?>">
        <button><?= L('EDIT') ?></button>
    </a>

    <button id="hide-cates" data-last="show"><?= L('HIDE_CATE') ?></button>

    <button id="add-httpapi"><?= L('ADD_API') ?></button>

    <button id="add-cate"><?= L('ADD_CATE') ?></button>

    <a href="<?= lrn("/tool/httpapi/projects/{$project->id}/envs") ?>">
        <button id="manage-env"><?= L('MANAGE_ENV') ?></button>
    </a>
</h2>

<div style="display:flex">
    <div id="api-cates" style="width:30%;">
        <h4><?= L('DEFAULT_CATE') ?></h4>
        <div>
        <?php if($_apis = $project->apis(0)) : ?>
        <?php foreach($_apis as $_api) : ?>
            <a href="#"><?= $_api->name ?></a>
        <?php endforeach ?>
        <?php endif ?>
       </div>

        <?php if(isset($cates) && iteratable($cates)) : ?>
        <?php foreach($cates as $cate) : ?>
        <h4><?= $cate->name ?>
            <small><button onclick="editCate(<?= $cate->id ?>, '<?= $cate->name ?>')" style="float:right"><?= L('EDIT') ?></button></small>
            <small><button onclick="delCate(<?= $cate->id ?>, '<?= $cate->name ?>')" style="float:right"><?= L('DELETE') ?></button></small>
        </h4>
        <div>
        <?php if($apis = $cate->apis()) : ?>
        <?php foreach($apis as $api) : ?>
            <a href="#" style="color:blue">api in cate 1</a>
        <?php endforeach ?>
        <?php endif ?>
        </div>
        <?php endforeach ?>
        <?php endif ?>
    </div>

    <div id="httpapi_tabs" style="width:75%;">
      <ul>
        <li>
            <a href="#httpapi_tabs-1"><?= L('ADD_API') ?></a>
            <span class="ui-icon" role="presentation"></span>
        </li>
      </ul>

      <div id="httpapi_tabs-1"><?php echo ($apiForm = $this->section('/ldtdf/tool/httpapi/__api_form', [], true)); ?></div>
    </div>
</div>

<div id="cate-create-form" title="<?= L('CREATE_CATE') ?>" style="display:none">
    <form method="POST" action="<?= lrn("/tool/httpapi/projects/{$project->id}/cate/new") ?>">
        <input type="hidden" name="project" value="<?= $project->id ?>">
        <input name="name" required placeholder="<?= L('INPUT_TITLE') ?>" style="width:300px;">
    </form>
</div>

<!-- <pre><code id="json" class="language-json"></code></pre> -->

<?= $this->js([
    // 'js/http.min',
    // 'js/vkbeautify',
]) ?>

<?= $this->section('lib/jqueryui') ?>

<script type="text/javascript">
    $('#api-cates').accordion({
//        collapsible: true,
        heightStyle: "content",
        beforeActivate: function (event, ui) {
//            console.log(event, ui)
        }
    })

    // https://github.com/lil-js/http
    // lil.http.get('http://api.hcm.docker/api/members/current', {

    // }, function (err, res) {
        // console.log(err)
        
        // https://github.com/vkiryukhin/vkBeautify
        // $('#json').html(vkbeautify.json(JSON.stringify(err), 4))

        // console.log(res)
    // })

    $(function() {
        var
        apiTabs    = $('#httpapi_tabs').tabs()
        apiTabsCnt = 1

        tabTemplate = `
            <li>
                <a href='#{href}'>#{label}</a>
                <span class='ui-icon ui-icon-close' role='presentation'></span>
            </li>
        `
      $('#add-httpapi').click(function() {
          addHttpApi()
      })

      $('#hide-cates').click(function () {
          if ($(this).data('last') == 'show') {
              $('#api-cates').hide()
              $('#httpapi_tabs').width('100%')
              $(this).html('<?= L("SHOW_CATE") ?>').data('last', 'hide')
          } else {
              $('#api-cates').show()
              $('#httpai_tabs').width('75%')
              $(this).html('<?= L("HIDE_CATE") ?>').data('last', 'show')
          }
      })

      $('#cate-create-form').dialog({
          autoOpen: false,
          height: 200,
          width: 380,
          modal: true,
          buttons: {
            "<?= L('CREATE') ?>": function () {
                var name = $('#cate-create-form form input[name="name"]').val()
                if (name) {
                    $('#cate-create-form form').submit()
                }
            },
            "<?= L('CANCEL') ?>": function () {
                $(this).dialog('close')
                $('#cate-create-form form input[name="name"]').val('')
            }
          },
          close: function () {
             $(this).dialog('close')
             $('#cate-create-form form input[name="name"]').val('')
          }
      })

      $('#add-cate').click(function () {
        $('#cate-create-form').dialog('open')    
      })
      
      $('.edit-cate').click(function () {
          console.log(this)
      })  
      
      $('#add-env').click(function () {
          
      })

      function addHttpApi() {
          var
          label = '<?= L('ADD_API') ?>',
          id    = 'httpapi_tabs_' + apiTabsCnt,
          li    = $(tabTemplate.replace(/#\{href\}/g, '#' + id).replace(/#\{label\}/g, label))

          apiTabs.find('.ui-tabs-nav').append(li)
          apiTabs.append(`<div id="${id}"><?= $apiForm ?></p></div>`)
          apiTabs.tabs('refresh')
          apiTabs.tabs({active: apiTabsCnt++})
      }
    
      apiTabs.delegate('span.ui-icon-close', 'click', function() {
          var panelId = $(this).closest('li').remove().attr('aria-controls')
          $('#' + panelId).remove()
          apiTabs.tabs('refresh')
      })
    })

    function syncApiTitle(obj) {
        var title = obj.value ? obj.value : 'new api'

        $('#httpapi_tabs ul:first li:eq(' + $('#httpapi_tabs').tabs('option', 'active') + ') a').html(title)
    }
    function delCate(id, name) {
        var
            deleteUrl  = '/tool/httpapi/projects/' + <?= $project->id ?> + '/cate/' + id + '/delete', 
            deleteForm = `<form method="POST" action="${deleteUrl}"></form>`
            cateName   = '<?= L('DELETE_CATE') ?> : ' + name

        $('<div title="' + cateName + '">').html(deleteForm).dialog({
            height: 100,
            width: 380,
            buttons: {
                "<?= L('CONFIRM') ?>": function () {
                    var deleteForm = $($(this).find('form')[0])
                    if (deleteForm.length > 0) {
                       deleteForm.submit() 
                    }
                },
                "<?= L('CANCEL') ?>": function () {
                    $(this).dialog('close')
                }
            }
        })
    }
    function editCate(id, nameOld) { 
        var
            updateUrl  = '/tool/httpapi/projects/' + <?= $project->id ?> + '/cate/' + id + '/edit', 
            updateForm = `
<form method="POST" action="${updateUrl}">
<input type="hidden" name="project" value="<?= $project->id ?>">
<input name="name" value="${nameOld}" required placeholder="<?= L('INPUT_TITLE') ?>" style="width:300px;">
</form>
`
        $('<div title="<?= L('EDIT_CATE') ?>">').html(updateForm).dialog({
            height: 200,
            width: 380,
            buttons: {
                "<?= L('UPDATE') ?>": function () {
                    var updateForm = $($(this).find('form')[0])
                    if ((updateForm.length > 0) && updateForm.find('input[name="name"]').val()) {
                       updateForm.submit() 
                    }
                },
                "<?= L('CANCEL') ?>": function () {
                    $(this).dialog('close')
                }
            }
        })
    }
</script>

