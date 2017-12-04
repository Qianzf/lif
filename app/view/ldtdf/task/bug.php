<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_BUG') ?></small>
    <span class="text-info">]</span>

    <i>
        <a href="/dep/bugs/<?= $bug->id ?>">
            B<?= $bug->id ?>:
            <?= $bug->title ?>
        </a>
    </i>
</p>

<?= $this->section('bug') ?>
