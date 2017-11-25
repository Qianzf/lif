<?= $this->layout('main') ?>
<?= $this->title([lang('BUG_LIST'), lang('LDTDFMS')]) ?>

<?= $this->section('back2list', [
    'model'  => $story,
    'key'    => 'STORY',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/stories',
]) ?>
