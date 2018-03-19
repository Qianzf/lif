<?php if ($model->alive()): ?>
<label><button id="assign-to">
    <?= L('ASSIGN') ?>
</button></label>

<div id="task-assign-form"
title="<?= L("ASSIGN_{$key}", $model->title) ?>"
class="invisible-default">
    <form method="POST" action="<?= $route ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="dependency" value="yes">
        <label>
          <span class="label-title">
              <?= L('ACTION') ?>
          </span>
          <select name="action" required>
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
              <?= L('TASK_BRANCH') ?>
          </span>
          <input
          placeholder="<?= L('ENSURE_PROJECT_REMOTE_BRANCH_EXISTS') ?> !!!"
          type="text"
          name="branch"
          value="<?= $branch ?? null ?>"
          required>
        </label>

        <label>
            <span class="label-title">
                <?= L('PROJECT_CONFIG') ?>
            </span>
            <textarea
            placeholder="<?= L('OPTIONAL') ?>"
            name="config"><?= $config ?? null ?></textarea>
        </label>

        <?php $manually = $manually ?? false; ?>
        <?php  ?>
        <label>
            <span class="label-title">
                <?= L('NEED_MANUALLY_HELP') ?>
            </span>
            <span><?= L('NO') ?></span>
            <input
            <?php if (! $manually): ?>
            checked
            <?php endif ?>
            type="radio"
            name="manually"
            value="no">
            <span><?= L('YES') ?></span>
            <input
            <?php if ($manually): ?>
            checked
            <?php endif ?>
            type="radio"
            name="manually"
            value="yes">
        </label>
        <label
        <?php if (! $manually): ?>
        class="invisible-default"
        <?php endif ?>
        id="assign-notes">
            <span class="label-title">
                <?= L('REMARKS') ?>
            </span>
            <textarea
            placeholder="<?= L('NOTE_STH_USEFUL') ?>"
            name="assign_notes"><?= $remarks ?? null ?></textarea>
        </label>
    </form>
</div>
<script type="text/javascript">
    $('#assign-to').click(function (e) {
        e.preventDefault()
        dialog = $("#task-assign-form").dialog({
          autoOpen: true,
          height: 550,
          width: '45%',
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
            removeSelectedResult()
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
    function removeAllSelectedResult() {
        $('input[name="assign_to"]').val('')
        $('input[name="branch"]').val('')
        $('textarea[name="assign_notes"]').val('')
    }
</script>
<?= $this->section('lib/jqueryui') ?>
<?php endif ?>
