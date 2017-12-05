<?php $story->type  = 'STORY'; ?>
<?php $story->_type = 'S'; ?>

<?= $this->section('task-dispatch-origin', [
    'taskOrigin'      => $story,
    'originEditRoute' => "/dep/stories/{$story->id}/edit",
    'taskAddRoute'    => "/dep/tasks/new?story={$story->id}",
]) ?>
