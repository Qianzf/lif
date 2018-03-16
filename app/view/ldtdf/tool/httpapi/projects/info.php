<?= $this->layout('main') ?>
<?= $this->title('HTTP API '.ldtdf('PROJECT')) ?>
<?= $this->section('common') ?>

<h2>
    <sub><code><?= "#{$project->id}" ?></code></sub>
    <big><?= $project->name ?></big>

    <?= $this->section('back_to', [
        'route' => lrn('tool/httpapi'),
    ]) ?>

    <a href='<?= lrn("tool/httpapi/projects/{$project->id}/edit") ?>'>
        <button><?= L('EDIT') ?></button>
    </a>

    <button id="add-httpapi"><?= L('ADD_API') ?></button>

    <a href='<?= lrn("tool/httpapi/cates/new?project={$project->id}") ?>'>
        <button><?= L('ADD_CATE') ?></button>
    </a>

    <a href='<?= lrn("tool/httpapi/env/new?project={$project->id}") ?>'>
        <button><?= L('ADD_ENV') ?></button>
    </a>
</h2>

<div id="dialog-add-httpapi" title="new http api" style="display: none;">
  <form>
    <label for="httpapi_path">
        路径
        <input id="httpapi_path">
    </label>

    <label for="httpapi_method">
        方法
        <select id="httpapi_method">
            <option value="get">GET</option>
            <option value="get">POST</option>
            <option value="get">PUT</option>
            <option value="get">PATCH</option>
            <option value="get">DELETE</option>
        </select>
    </label>

    <label for="httpapi_path">
        路径
        <input id="httpapi_path">
    </label>
  </form>
</div>

<div id="httpapi_tabs">
  <ul>
    <li>
        <a href="#httpapi_tabs-1">api tab default</a>
        <span class="ui-icon ui-icon-close" role="presentation">
            移除标签页
        </span>
    </li>
  </ul>

  <div id="httpapi_tabs-1">
  </div>
</div>

<!-- <pre><code id="json" class="language-json"></code></pre> -->

<?= $this->js([
    // 'js/http.min',
    // 'js/vkbeautify',
]) ?>

<?= $this->section('lib/jqueryui') ?>

<script type="text/javascript">
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
            <span class='ui-icon ui-icon-close' role='presentation'>
                Remove Tab
            </span>
            </li>
        `

        console.log(apiTabs)
    
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
    
      function addHttpApi() {
        var
        label = 'new http api',
        id = 'httpapi_tabs_' + apiTabsCnt,
        li = $(tabTemplate.replace(/#\{href\}/g, '#' + id).replace(/#\{label\}/g, label)),

        apiPathHtml = "新增 API " + apiTabsCnt

        apiTabs.find(".ui-tabs-nav").append(li );

        apiTabs.append( "<div id='" + id + "'><p>" + apiPathHtml + "</p></div>" );
        apiTabs.tabs('refresh')
      }
    
      apiTabs.delegate( "span.ui-icon-close", "click", function() {
        var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
        $( "#" + panelId ).remove();

        apiTabs.tabs('refresh')
      });
    });
</script>
