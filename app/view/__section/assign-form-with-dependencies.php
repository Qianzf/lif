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
              <?= lang('ACTION') ?>
          </span>
          <select name="action" required>
            <option value="WAITTING_DEP2TEST">
              <?= lang("ASSIGN_WAITTING_DEP2TEST") ?>
            </option>
          </select>
        </label>
        <label>
          <span class="label-title">
              <?= lang('TASK_BRANCH') ?>
          </span>
          <input
          placeholder="<?= lang('ENSURE_PROJECT_REMOTE_BRANCH_EXISTS') ?> !!!"
          type="text"
          name="branch"
          required>
        </label>

        <label>
            <span class="label-title">
                <?= lang('WHETHER_NEED_MANUALLY_HELP') ?>
            </span>
            <span><?= lang('NO') ?></span>
            <input type="radio" name="manually" value="no" checked>
            <span><?= lang('YES') ?></span>
            <input type="radio" name="manually" value="yes">
        </label>

        <label class="invisible-default" id="assign-notes">
            <span class="label-title">
                <?= lang('REMARKS') ?>
            </span>
            <textarea
            placeholder="<?= lang('NOTE_STH_USEFUL') ?>"
            name="assign-notes"></textarea>
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
            $('#task-assign-form input,textarea').val('')
            $('#selected-search-res span').html('')
          }
        })
    })
    $('input:radio[name="manually"]').change(function () {
        let assignNotes = $('#assign-notes')
        if ('yes' == this.value) {
            assignNotes.show()
        } else {
            assignNotes.hide()
        }
    })
</script>
<?= $this->section('lib/jqueryui') ?>
<?php endif ?>
