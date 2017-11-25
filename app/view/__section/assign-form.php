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
                <?= lang('ASSIGN_TO') ?>
            </span>
            <input type="text" name="assign-to">
        </label>
        <label>
            <span class="label-title">
                <?= lang('NOTES') ?>
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
          height: 300,
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
