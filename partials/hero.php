<?php
/**
 * @var array $langarr;
 */
?>

<div class="hero">
    <div class="hero__inner container">
        <div class="hero__content">
            <div class="hero__logo">
                <picture>
                    <img src="images/hero/5head-logo.png" srcset="images/hero/5head-logo@2x.png 2x" loading="lazy" alt="5head">
                </picture>
            </div>

            <div class="hero__content-text">
                <h3 class="hero__title">
                    <?= $langarr['hero']['title'] ?>
                </h3>

                <button class="hero__button button js-start" type="button">
                    <?= $langarr['hero']['button'] ?>
                </button>
            </div>
        </div>
    </div>
</div>