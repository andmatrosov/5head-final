<?php
$dividerItems = [
        'images/divider/icons/1',
        'images/divider/icons/2',
        'images/divider/icons/3',
        'images/divider/icons/4',
        'images/divider/icons/5',
        'images/divider/icons/6',
        'images/divider/icons/7',
        'images/divider/icons/8'
    ];


/**
 * @var string $link;
 */
?>

<div class="divider">
    <div class="divider__wrapper" role="marquee">

        <?php foreach($dividerItems as $i => $item): ?>

        <<?= $i == 7 ? "a href=\"$link\" target=\"_blank\" " : "div"?> class="divider__item">
            <picture>
                <source srcset="<?= $item ?>.webp 1x, <?= $item ?>@2x.webp 2x" type="image/webp">
                <img src="<?= $item ?>.png" alt="" srcset="<?= $item ?>@2x.png 2x" loading="lazy"/>
            </picture>
        </<?= $i == 7 ? "a" : "div"?>>

        <?php endforeach; ?>

    </div>
</div>