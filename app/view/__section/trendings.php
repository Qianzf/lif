<?php if (isset($trendings) && iteratable($trendings)) : ?>
<?php foreach ($trendings as $trending) : ?>
<ul>
    <li>
        <?php
            $user = $trending->user();
            $name = (share('user.id') == $user->id)
            ? lang('YOU') : (
                $user->name ?? lang('UNKNOWN_USER')
            );
        ?>
        <?= $trending->at, ' , ', lang("ROLE_{$user->role}") ?>
        <i>
            <a href="/dep/users/<?= $user->id ?>">
                <?= $user->name ?>
            </a>
        </i>
        <?= $trending->genHTMLStringOfEvent($displayShort ?? null) ?>        
    </li>
</ul>
<?php endforeach ?>
<?php endif ?>