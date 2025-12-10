<?php
/**
 * @var array $langarr
 * @var array $chunks
 */
?>

<div class="winners">
    <div class="winners__inner container">
        <h2 class="winners__title"><?= strip_tags($langarr['winners']['email']) ?></h2>
        <div class="winners__body">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($chunks as $chunk) : ?>
                        <div class="swiper-slide">
                            <table class="winners__table">
                                <thead>
                                <tr>
                                    <th><?= $langarr['winners']['email'] ?></th>
                                    <th><?= $langarr['winners']['scores'] ?></th>
                                    <th><?= $langarr['winners']['prize'] ?></th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($chunk as $winner): ?>
                                    <tr>
                                        <td><?= $winner['email'] ?></td>
                                        <td><?= $winner['score'] ?></td>
                                        <td><?= $winner['prize'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="winners__navigation">
            <button class="cross-button winners-button-prev" type="button">
                <img src="images/svg/arrow.svg" alt="Prev"/>
            </button>
            <button class="cross-button winners-button-next" type="button">
                <img src="images/svg/arrow.svg" alt="Next"/>
            </button>
        </div>
    </div>
</div>

<div class="winners winners--mobile">
    <div class="winners__inner container">
        <h2 class="winners__title"><?= $langarr['winners']['email'] ?></h2>
        <div class="winners__body">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($chunks as $chunk) : ?>
                    <div class="swiper-slide">
                        <table class="winners__table">
                            <thead>
                            <tr>
                                <th><?= $langarr['winners']['email'] ?></th>
                                <th><?= $langarr['winners']['scores'] ?><br/><?= $langarr['winners']['prize'] ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($chunk as $winner): ?>
                                <tr>
                                    <td><?= $winner['email'] ?></td>
                                    <td><?= $winner['score'] ?><br/><?= $winner['prize'] ?></td>
                                </tr>
                            <?php  endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="winners__navigation">
            <button class="cross-button winners-button-prev" type="button">
                <img src="images/svg/arrow.svg" alt="Prev"/>
            </button>
            <button class="cross-button winners-button-next" type="button">
                <img src="images/svg/arrow.svg" alt="Next"/>
            </button>
        </div>
    </div>
</div>