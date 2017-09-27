<?= $this->layout('main') ?>
<?= $this->title([lang('TASK_LIST'), lang('LDTDFMS')]) ?>
<?= $this->section('search') ?>

<?php if (isset($trending) && $trending) { ?>
<?php foreach ($trending as $event) { ?>
<ul>
    <li>
        <?=
            (
                $event->at
                .' , '
                .($event->user()->name)
                .' '
                .$event->detail
            )
        ?>
    </li>
</ul>
<?php } ?>
<?php } ?>
