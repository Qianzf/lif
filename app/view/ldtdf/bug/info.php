<?php $bug->type  = 'BUG'; ?>
<?php $bug->_type = 'B'; ?>

<?= $this->section('task-dispatch-origin', [
    'taskOrigin'      => $bug,
    'originEditRoute' => lrn("bugs/{$bug->id}/edit"),
    'taskAddRoute'    => lrn("tasks/new?bug={$bug->id}"),
]) ?>
