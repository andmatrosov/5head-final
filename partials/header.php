<?php
/**
 * @var array $langarr
 */
?>

<header class="header">
    <div class="header__inner container">
        <a href="/" class="header__logo logo">
            <img class="logo__image" src="./images/logo.svg" alt="Positivus" width="168" height="36" loading="lazy"/>
        </a>
        <select name="languange" id="lang" class="header__language select">
            <?php foreach($langarr['langs'] as $lang):
                $selected = $lang === $_SESSION['lang'] ? "selected" : "";
            ?>
            <option value="<?= $lang ?>" <?= $selected ?>><?= strtoupper(explode('-', $lang)[0]) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</header>