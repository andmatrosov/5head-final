<?php
/**
 * @var array $langarr;
 * @var string $link;
 */
?>

<div class="hero">
    <div class="hero__inner container">
        <div class="hero__content">
            <div class="hero__logo">
                <picture>
                    <source srcset="images/hero/5head-logo.webp 1x, images/hero/5head-logo@2x.webp 2x" type="image/webp">
                    <img src="images/hero/5head-logo.png" srcset="images/hero/5head-logo@2x.png 2x" loading="lazy" alt="5head">
                </picture>
            </div>

            <div class="hero__content-text">
                <h3 class="hero__title">
                    <?= $langarr['hero']['title'] ?>
                </h3>

                <?php if(isset($_COOKIE['finished'])) {  ?>
                    <a href="<?= $link ?>" class="hero__button button">
                        <?= $langarr['buttons']['getBonus'] ?>
                    </a>
                <?php } else { ?>
                    <button class="hero__button button js-start" type="button">
                        <?= $langarr['hero']['button'] ?>
                    </button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>