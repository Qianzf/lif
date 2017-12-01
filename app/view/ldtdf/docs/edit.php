<?= $this->layout('main') ?>
<?= $this->title([L('BUG_LIST'), L('LDTDFMS')]) ?>

<?= $this->section('back2list', [
    'model'  => $doc,
    'key'    => 'DOC',
    'action' => ($editable ? null : 'VIEW'),
    'route'  => '/dep/docs',
]) ?>
