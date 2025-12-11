<?php
/**
 * @var array $langarr;
 * @var string $videoInsani;
 */

$hints = [
    [
        'name' => 'QIKERT',
        'text' => $langarr['quiz']['hints'][0],
        'video' => '01'
    ],
    [
        'name' => 'WOXIC',
        'text' => $langarr['quiz']['hints'][1],
        'video' => '02'
    ],
    [
        'name' => 'INSANI',
        'text' => $langarr['quiz']['hints'][2],
        'video' => '03'
    ],
    [
        'name' => 'WOXIC',
        'text' => $langarr['quiz']['hints'][3],
        'video' => '04'
    ],
    [
        'name' => 'INSANI',
        'text' => $langarr['quiz']['hints'][4],
        'video' => '05'
    ],
    [
        'name' => 'WOXIC',
        'text' => $langarr['quiz']['hints'][5],
        'video' => '06'
    ],
    [
        'name' => 'WOXIC',
        'text' => $langarr['quiz']['hints'][6],
        'video' => '07'
    ],
    [
        'name' => 'INSANI',
        'text' => $langarr['quiz']['hints'][7],
        'video' => '08'
    ],
    [
        'name' => 'INSANI',
        'text' => $langarr['quiz']['hints'][8],
        'video' => '09'
    ],
    [
        'name' => 'WOXIC',
        'text' => $langarr['quiz']['hints'][9],
        'video' => '10'
    ],
];


$link = 'https://cropped.link/5headreg';
?>


<dialog class="mobile-overlay" id="register">
    <div class="mobile-overlay__body">
        <div class="popup popup__register">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <div class="popup__content">
                <h3><?= $langarr['popups']['register']['title'] ?></h3>
                <form class="form__register" action="" method="post">
                    <div class="form__inputs">
                        <div class="form__group">
                            <label for="nickname"><?= $langarr['popups']['register']['label1'] ?></label>
                            <input type="text" id="nickname" name="nickname" placeholder="<?= $langarr['popups']['register']['placeholder1'] ?>" required>
                        </div>
                        <div class="form__group">
                            <label for="email"><?= $langarr['popups']['register']['label2'] ?></label>
                            <input type="email" id="email" name="email" placeholder="<?= $langarr['popups']['register']['placeholder2'] ?>" required>
                        </div>
                    </div>
                    <span class="form__info"><?= $langarr['popups']['register']['text'] ?></span>
                    <span class="error_message"></span>
                    <button type="submit" class="button"><?= $langarr['popups']['register']['btn'] ?></button>
                </form>
            </div>
        </div>
    </div>
</dialog>

<dialog class="mobile-overlay" id="gameOver">
    <div class="mobile-overlay__body">
        <div class="popup popup__gameover">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <?php
                $randomChoice1 = rand(0, 1);
                if ($randomChoice1 === 0) {
            ?>
                <div class="popup__content">
                    <h3><?= $langarr['popups']['gameOver'][0]['title'] ?></h3>
                    <p><?= $langarr['popups']['gameOver'][0]['text'] ?></p>
                    <a class="button" href="<?= $link ?>" target="_blank"><?= $langarr['buttons']['goAway'] ?></a>
                </div>
            <?php } else { ?>
                <div class="popup__content">
                    <h3><?= $langarr['popups']['gameOver'][1]['title'] ?></h3>
                    <p><?= $langarr['popups']['gameOver'][1]['text'] ?></p>
                    <a class="button" href="<?= $link ?>" target="_blank"><?= $langarr['buttons']['goAway'] ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</dialog>

<dialog class="mobile-overlay" id="likeInsani">
    <div class="mobile-overlay__body">
        <div class="popup popup__gameover">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <?php
            $randomChoice1 = rand(0, 1);
            if ($randomChoice1 === 0) {
            ?>
                <div class="popup__content">
                    <h3><?= $langarr['popups']['likeInsani']['title'] ?></h3>
                    <p><?= $langarr['popups']['likeInsani']['text'] ?></p>
                    <a class="button" href="<?= $videoInsani ?>" target="_blank"><?= $langarr['buttons']['goAway'] ?></a>
                </div>
            <?php } else { ?>
                <div class="popup__content">
                    <h3><?= $langarr['popups']['likeInsani']['title'] ?></h3>
                    <p><?= $langarr['popups']['likeInsani']['text'] ?></p>
                    <a class="button" href="<?= $videoInsani ?>" target="_blank"><?= $langarr['buttons']['goAway'] ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</dialog>

<dialog class="mobile-overlay" id="likeMaj3r">
    <div class="mobile-overlay__body">
        <div class="popup popup__gameover">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <div class="popup__content">
                <h3><?= $langarr['popups']['likeMaj3r']['title'] ?></h3>
                <p><?= $langarr['popups']['likeMaj3r']['text'] ?></p>
                <a class="button" href="<?= $videoInsani ?>" target="_blank"><?= $langarr['buttons']['goAway'] ?></a>
            </div>
        </div>
    </div>
</dialog>

<dialog class="mobile-overlay" id="coolRes">
    <div class="mobile-overlay__body">
        <div class="popup popup__gameover">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <div class="popup__content">
                <h3><?= $langarr['popups']['coolRes']['title'] ?></h3>
                <p><?= $langarr['popups']['coolRes']['text'] ?></p>
                <a class="button" href="<?= $link ?>" target="_blank"><?= $langarr['buttons']['goAway'] ?></a>
            </div>
        </div>
    </div>
</dialog>

<dialog class="mobile-overlay" id="finish">
    <div class="mobile-overlay__body">
        <div class="popup popup__gameover">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <?php
                $randomChoice2 = rand(0, 1);
                if ($randomChoice2 === 0) {
            ?>
                <div class="popup__content">
                    <h3><?= $langarr['popups']['finish'][0]['title'] ?></h3>
                    <p><?= $langarr['popups']['finish'][0]['text'] ?></p>
                    <a class="button" href="<?= $link ?>" target="_blank"><?= $langarr['buttons']['getBonus'] ?></a>
                </div>
            <?php } else { ?>
                <div class="popup__content" style="display: none">
                    <h3><?= $langarr['popups']['finish'][1]['title'] ?></h3>
                    <p><?= $langarr['popups']['finish'][1]['text'] ?></p>
                    <a class="button" href="<?= $link ?>" target="_blank"><?= $langarr['buttons']['getBonus'] ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</dialog>

<dialog class="mobile-overlay" id="timeOver">
    <div class="mobile-overlay__body">
        <div class="popup popup__gameover">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <div class="popup__content">
                <h3><?= $langarr['popups']['timeOver']['title'] ?></h3>
                <p><?= $langarr['popups']['timeOver']['text'] ?></p>
                <a class="button" href="<?= $link ?>" target="_blank"><?= $langarr['buttons']['goAway'] ?></a>
            </div>
        </div>
    </div>
</dialog>

<dialog class="mobile-overlay" id="hints">
    <div class="mobile-overlay__body">
        <div class="popup popup__hints">
            <form class="mobile-overlay__close-button-wrapper" method="dialog">
                <button class="mobile-overlay__close-button cross-button" type="submit">
                    <span class="visually-hidden">Close navigation menu</span>
                </button>
            </form>

            <?php foreach($hints as $i => $hint): ?>
                <div class="popup__hints-content" data-hint-id="<?= $i ?>" style="display: none">
                    <div class="popup__hints-video">
                        <video
                            playsinline="playsinline"
                            disablepictureinpicture="disablepictureinpicture"
                            disableremoteplayback="disableremoteplayback"
                            aria-hidden="true"
                            preload="metadata">
                            <source src="video/hints/<?= $hint['video'] ?>.mp4" type="video/mp4"/>
                            <source src="video/hints/<?= $hint['video'] ?>.webm" type="video/webm"/>
                        </video>
                    </div>
                    <div class="popup__hints-body">
                        <h4 class="popup__hints-title"><?= $hint['name'] ?></h4>
                        <p class="popup__hints-text"><?= $hint['text'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</dialog>