<?php if (isset($trendings) && iteratable($trendings)) : ?>
<?php foreach ($trendings as $key => $trending) : ?>
<ul>
    <li>
    <small>
        <?php
            $user = $trending->user();
            $name = $user->name ?? L('UNKNOWN_USER');
        ?>
        <?= $trending->at, ' , ', L("ROLE_{$user->role}") ?>
        <i>
            <a href='<?= lrn("users/{$user->id}") ?>'>
                <?= $user->name ?>
            </a>
        </i>
        <?= $trending->genHTMLStringOfEvent(
                ($displayRefType ?? true),
                ($displayRefState ?? false)
            )
        ?>
    </small>

    <?php if ($displayComments ?? false): ?>
    <?php if (trim($trending->notes)): ?>
    <span class="stub"></span>
    <i class="fa fa-minus-square-o"
    onclick="hideOrShow(this, <?= $key ?>)"></i>
    <textarea
    id="comment-<?= $key ?>"
    class="comments"><?= $trending->notes ?></textarea>
    <?php endif ?>

    <script type="text/javascript">
        function hideOrShow(obj, key) {
            obj = $(obj)
            let comment = $('#comment-' + key)
            let remove = 'fa-plus-square-o'
            let add = 'fa-minus-square-o'
            if (obj.hasClass(remove)) {
                comment.show()
            } else {
                remove = 'fa-plus-square-o'
                add = 'fa-plus-square-o'
                comment.hide()
            }
            obj.removeClass(remove)
            obj.addClass(add)
        }
    </script>
    <?php endif ?>
    </li>
</ul>
<?php endforeach ?>
<?php endif ?>