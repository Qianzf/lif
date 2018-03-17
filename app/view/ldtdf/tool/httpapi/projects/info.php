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

    <button id="add-env"><?= L('ADD_ENV') ?></button>
</h2>

<div style="display:flex">
    <div id="api-cates" style="width:30%;">
        <h4>cate 1</h4>
        <div>
            <a href="#" style="color:blue">api in cate 1</a>
        </div>

        <h4>cate 2</h4>
        <div>
            <a href="#">api in cate 2</a>
        </div>
    
    </div>

    <div id="httpapi_tabs" style="width:75%">
      <ul>
        <li>
            <a href="#httpapi_tabs-1">api tab default</a>
            <span class="ui-icon" role="presentation"></span>
        </li>
      </ul>

      <div id="httpapi_tabs-1"><?php echo ($apiForm = $this->section('/ldtdf/tool/httpapi/__api_form', [], true)); ?></div>
    </div>
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
        heightStyle: "content"
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
    
      // var dialog = $('#dialog-add-httpapi').dialog({
      //   autoOpen: false,
      //   modal: true,
      //   buttons: {
      //     Add: function() {
      //       addHttpApi()
      //       $(this).dialog('close')
      //     },
      //     Cancel: function() {
      //       $(this).dialog('close')
      //     }
      //   },
      //   close: function() {
      //     // addHttpApiForm.reset()
      //   }
      // });

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

      $('#add-cate').click(function () {
          
      })
    
      $('#add-env').click(function () {
          
      })

       function addHttpApi() {
        var
        label = 'new http api',
        id    = 'httpapi_tabs_' + apiTabsCnt++,
        li    = $(tabTemplate.replace(/#\{href\}/g, '#' + id).replace(/#\{label\}/g, label))

        apiTabs.find('.ui-tabs-nav').append(li)
        apiTabs.append(`<div id="${id}"><?= $apiForm ?></p></div>`)
        apiTabs.tabs('refresh')
      }
    
      apiTabs.delegate('span.ui-icon-close', 'click', function() {
          var panelId = $(this).closest('li').remove().attr('aria-controls')
          $('#' + panelId).remove()
          apiTabs.tabs('refresh')
      })
    })
</script>

