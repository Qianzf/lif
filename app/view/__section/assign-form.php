<?php if ($model->isAlive()): ?>
<label><button id="assign-to">
    <?= lang('ASSIGN') ?>
</button></label>

<div id="task-assign-form"
title="<?= lang("ASSIGN_{$key}", $model->title) ?>"
class="invisible-default">
    <form method="POST" action="<?= $route ?>">
        <?= csrf_feild() ?>
        <label>
          <span class="label-title">
              <?= lang('ACTION') ?>
          </span>
          <select name="action" required>
            <option value="0">
              -- <?= lang('SELECT_ASSIGN_ACTION') ?> --
            </option>
            <?php if (isset($assigns) && iteratable($assigns)): ?>
            <?php foreach ($assigns as $order => $assign): ?>
              <option value="<?= $assign ?>">
                <?= lang("ASSIGN_{$assign}") ?>
              </option>
            <?php endforeach ?>
            <?php endif ?>
          </select>
        </label>
        <label>
            <span class="label-title">
                <?= lang('TARGET') ?>
            </span>
            <input type="hidden" name="assign-to" required>
            <?= $this->section('instant-search', [
                'api' => $api,
                'sresKeyInput' => 'assign-to',
                // 'sresKey' => '/api',
                // 'sresVal' => '/api',
            ]) ?>
        </label>
        <label>
            <span class="label-title">
                <?= lang('REMARKS') ?>
            </span>
            <textarea name="assign-notes"></textarea>
        </label>
    </form>
</div>
<script type="text/javascript">
    $('#assign-to').click(function (e) {
        e.preventDefault()
        dialog = $("#task-assign-form" ).dialog({
          autoOpen: true,
          height: 500,
          width: '70%',
          modal: true,
          buttons: {
            '<?= lang('CONFIRM') ?>': function () {
                $(this).find('form').submit()
            },
            '<?= lang('CANCEL') ?>': function() {
              dialog.dialog('close')
            }
          },
          close: function() {
            $('input[name="assign-to"]').val('')
            $('textarea[name="assign-notes"]').val('')
          }
        })
    })
</script>
<?= $this->section('lib/jqueryui') ?>
<?php endif ?>
