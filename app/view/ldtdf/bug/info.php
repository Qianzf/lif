<?php $bug->type  = 'BUG'; ?>
<?php $bug->_type = 'B'; ?>

<?= $this->section('task-dispatch-origin', [
    'taskOrigin'      => $bug,
    'originEditRoute' => "/dep/bugs/{$bug->id}/edit",
    'taskAddRoute'    => "/dep/tasks/new?bug={$bug->id}",
]) ?>
