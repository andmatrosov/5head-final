<?php
$link = 'https://cropped.link/5headreg';
$videoInsani = 'https://www.youtube.com/watch?v=3CeV3dv6q7c';

// Получаем язык браузера
$acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
$browserLang = explode(',', $acceptLanguage)[0]; // Берём до первой запятой

// Доступные языки
$availableLangs = ['ru', 'en', 'pt-BR', 'es', 'tr'];

// Определяем язык из браузера
$lang = 'en'; // По умолчанию английский

// Проверяем точное совпадение (например, pt-BR, es-LATAM)
if (in_array($browserLang, $availableLangs)) {
    $lang = $browserLang;
} else {
    // Если нет точного совпадения, берём первые 2 буквы
    $shortLang = substr($browserLang, 0, 2);

    // Проверяем сокращённый вариант
    if (in_array($shortLang, $availableLangs)) {
        $lang = $shortLang;
    } else {
        // Для испанского - если браузер es-*, выбираем es по умолчанию
        if ($shortLang === 'es') {
            $lang = 'es';
        } else if ($shortLang === 'pt') {
            $lang = 'pt-BR';
        }
    }
}

session_start();

// Устанавливаем язык из сессии или определённый выше
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $lang;
}

// Переключение языка через GET параметр
if (isset($_GET['lang']) && !empty($_GET['lang'])) {
    $requestedLang = $_GET['lang'];

    // Проверяем что запрошенный язык есть в списке доступных
    if (in_array($requestedLang, $availableLangs)) {
        $_SESSION['lang'] = $requestedLang;
    }
}

// Подключаем файл перевода
require_once "languages/" . $_SESSION['lang'] . ".php";

/**
 * @var array $langarr
 */


function getCurrentDomain() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . '://' . $host;
}

$url = getCurrentDomain() . '/backend/api/get-winners.php';
$response = @file_get_contents($url);

$winners = [];

if ($response !== false) {
    $result = json_decode($response, true);

    // Проверяем структуру ответа
    if (isset($result['success']) && $result['success'] === true && isset($result['data'])) {
        $winners = $result['data'];
    }
}

$chunks = !empty($winners) ? array_chunk($winners, 5) : [];

$urlStatus = getCurrentDomain() . '/backend/api/get-contest-status.php';
$responseStatus = @file_get_contents($urlStatus);

$status = true;

if ($responseStatus !== false) {
    $result = json_decode($responseStatus, true);

    // Проверяем структуру ответа
    if (isset($result['success']) && $result['success'] === true && isset($result['data'])) {
        $status = $result['is_active'];
    }
}
?>

<!doctype html>
<html lang="<?= $_SESSION['lang'] ?>">
<?php require_once 'partials/head.php'; ?>
<body>


<?php require_once 'partials/header.php'; ?>

    <main class="main">
        <?php
            require_once 'partials/hero.php';
            require_once 'partials/divider.php';
            if($status) {
                require_once 'partials/quiz.php';
            }
            require_once 'partials/prizes.php';
//            if(isset($_COOKIE['finished'])) {
            if(!$status) {
                require_once 'partials/winners.php';
            }
        ?>
    </main>

    <?php require_once 'partials/popups.php'; ?>
    <script src="js/libs/ux-select.iife.js"></script>
    <script src="js/libs/vanilla-tilt.min.js"></script>
    <script src="js/libs/gsap.js"></script>
    <script src="js/libs/howler.min.js"></script>
    <script src="js/libs/swiper-bundle.min.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/soundManager.js"></script>
    <script src="js/quiz.js"></script>
    <script src="js/register.js"></script>
    <script src="js/main.js"></script>
</body>
</html>