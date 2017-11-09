<?= $this->layout('main') ?>
<?= $this->section('back2list', [
    'model' => $bug,
    'key'   => 'BUG',
    'route' => '/dep/bugs',
]) ?>
