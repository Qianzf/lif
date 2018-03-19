<label><button id="update-task-env-button">
    <?= $title = L('UPDATE_ENV') ?>
</button></label>

<div
id="task-env-update-form"
class="invisible-default"
title="<?= $title ?>">
    <form method="POST" action="<?= $action ?>">
        <?= csrf_field() ?>
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
    </form>
</div>

<?= $this->section('lib/jqueryui') ?>
<script type="text/javascript">
    $('#update-task-env-button').click(function (e) {
        e.preventDefault()
        dialog = $("#task-env-update-form").dialog({
          autoOpen: true,
          height: 350,
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
          }
        })
    })
</script>