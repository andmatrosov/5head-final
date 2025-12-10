<?php
/**
 * @var array $langarr;
 */
    $prizes = [
        [
            'img' => 'prize-1',
            'alt' => 'Talon Knife | Slaughter',
            'text' => $langarr['prizes']['items'][0]
        ],
        [
            'img' => 'prize-2',
            'alt' => 'Skeleton Knife | Damascus Steel',
            'text' => $langarr['prizes']['items'][1]
        ],
        [
            'img' => 'prize-3',
            'alt' => 'AK-47| Azimov',
            'text' => $langarr['prizes']['items'][2]
        ],
        [
            'img' => 'prize-4',
            'alt' => 'M4A1-S | Nightmare',
            'text' => $langarr['prizes']['items'][3]
        ],
        [
            'img' => 'prize-5',
            'alt' => '$5 Promocode',
            'text' => $langarr['prizes']['items'][4]
        ]
    ];
?>

<div class="prizes">
    <div class="prizes__inner container">
        <h2 class="prizes__title"><?= $langarr['prizes']['title'] ?></h2>

        <div class="prizes__items">
            <div class="prizes__item prizes__item-1 main" data-title="<?= strip_tags($prizes[0]['text']) ?>">
                <div class="prizes__item-content" data-effect>
                    <div class="prizes__item-img">
                        <picture>
                            <source srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>-mob.png 1x, images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>.png 2x" type="image/png" media="(max-width: 767px)">
                            <source srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>-mob.webp 1x, images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>.webp 2x" type="image/webp" media="(max-width: 767px)">
                            <source srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>.webp 1x, images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>@2x.png 2x" type="image/webp">
                            <img
                                src="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>.png"
                                srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prizes[0]['img'] ?>@2x.png 2x"
                                alt="<?= $prizes[0]['text'] ?>"/>
                        </picture>
                    </div>
                    <p class="prizes__item-text">
                        <?= $prizes[0]['text'] ?>
                    </p>
                </div>
            </div>
            <div class="prizes__row">
                <?php foreach($prizes as $i => $prize): ?>
                    <?php if($i !== 0): ?>
                        <div class="prizes__item prizes__item-<?= $i + 1 ?>" data-title="<?= strip_tags($prize['text']) ?>">
                            <div class="prizes__item-content" data-effect>
                                <div class="prizes__item-img">
                                    <picture>
                                        <source srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>-mob.png 1x, images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>.png 2x" type="image/png" media="(max-width: 767px)">
                                        <source srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>-mob.webp 1x, images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>.webp 2x" type="image/webp" media="(max-width: 767px)">
                                        <source srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>.webp 1x, images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>@2x.png 2x" type="image/webp">
                                        <img src="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>.png" srcset="images/prizes/<?= $_SESSION['lang'] ?>/<?= $prize['img'] ?>@2x.png 2x" alt="<?= $prize['alt'] ?>"/>
                                    </picture>
                                </div>
                                <p class="prizes__item-text">
                                    <?= $prize['text'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>