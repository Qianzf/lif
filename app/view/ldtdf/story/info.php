<?php $story->type  = 'STORY'; ?>
<?php $story->_type = 'S'; ?>

<?= $this->section('task-dispatch-origin', [
    'taskOrigin'      => $story,
    'originEditRoute' => lrn("stories/{$story->id}/edit"),
    'taskAddRoute'    => lrn("tasks/new?story={$story->id}"),
]) ?>
