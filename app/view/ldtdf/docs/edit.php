<?= $this->layout('main') ?>
<?= $this->title([lang('BUG_LIST'), lang('LDTDFMS')]) ?>

<?= $this->section('back2list', [
    'model'  => $doc,
    'key'    => 'DOC',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/docs',
]) ?>
