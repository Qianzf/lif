<p>
    <span class="stub-2"></span>
    <span class="text-info">[</span>
    <small><?= L('RELATED_STORY') ?></small>
    <span class="text-info">]</span>

    <i>
        <a href="/dep/stories/<?= $story->id ?>">
            S<?= $story->id ?>:
            <?= $story->title ?>
        </a>
    </i>
</p>

<?= $this->section('story') ?>
