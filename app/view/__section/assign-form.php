<?php if ($model->isAlive()): ?>
<label><button id="assign-to">
    <?= L('ASSIGN') ?>
</button></label>

<div id="task-assign-form"
title="<?= L("ASSIGN_{$key}", $model->title) ?>"
class="invisible-default">
    <form method="POST" action="<?= $route ?>">
        <?= csrf_feild() ?>
        <label>
          <span class="label-title">
              <?= L('ACTION') ?>
          </span>
          <select name="action" required>
            <option value="0">
              -- <?= L('SELECT_ASSIGN_ACTION') ?> --
            </option>
            <?php if (isset($assigns) && iteratable($assigns)): ?>
            <?php foreach ($assigns as $order => $assign): ?>
              <option value="<?= $assign ?>">
                <?= L("ASSIGN_{$assign}") ?>
              </option>
            <?php endforeach ?>
            <?php endif ?>
          </select>
        </label>
        <label>
            <span class="label-title">
                <?= L('TARGET') ?>
            </span>
            <input type="hidden" name="assign_to" required>
            <?= $this->section('instant-search', [
                'api' => $api,
                'sresKeyInput' => 'assign_to',
                // 'sresKey' => '/api',
                // 'sresVal' => '/api',
            ]) ?>
        </label>
        <label>
            <span class="label-title">
                <?= L('REMARKS') ?>
            </span>
            <textarea name="assign_notes"></textarea>
        </label>
    </form>
</div>
<script type="text/javascript">
    $('#assign-to').click(function (e) {
        e.preventDefault()
        dialog = $("#task-assign-form" ).dialog({
          autoOpen: true,
          height: 450,
          width: '30%',
          modal: true,
          buttons: {
            '<?= L('CONFIRM') ?>': function () {
                $(this).find('form').submit()
            },
            '<?= L('CANCEL') ?>': function() {
              dialog.dialog('close')
            }
          },
          close: function() {
              removeAllSelectedResult()
          }
        })
    })
    function removeAllSelectedResult() {
        $('input[name="assign_to"]').val('')
        $('textarea[name="assign_notes"]').val('')
    }
</script>
<?= $this->section('lib/jqueryui') ?>
<?php endif ?>
