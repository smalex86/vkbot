<?php 

//namespace Smalex86\VkInviter;

## Главный конфигурационный файл
error_reporting(E_ALL);

if (!defined("PATH_SEPARATOR")) {
    define("PATH_SEPARATOR", getenv("COMSPEC")? ";" : ":");
}
if (!defined("DIRECTORY_SEPARATOR")) {
    define("DIRECTORY_SEPARATOR ", "/");
}

ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.dirname(__FILE__)); // корневая папка
ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.dirname(__FILE__).'/src'); // папка с классами
ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.dirname(__FILE__).'/src/include'); // папка include
ini_set("session.use_trans_sid", true); // поддержка использования SID (идентификатора сессии)

ini_set('display_errors', 'On'); // показывать ошибки - на готовом продукте убрать
date_default_timezone_set('Asia/Krasnoyarsk'); // установка временной зоны для скриптов

// подключение файла с секретными настройками
$secretFileName = __DIR__ . '/secret.php';
if (file_exists($secretFileName)) {
  include_once 'secret.php';
}  
// проверка существования переменной $secret
if (!isset($secret)) {
  die('Не найдены секретные настройки.');
}

// константы для подключения к бд
define("DB_HOST", $secret['db']['host']);
define("DB_USERNAME", $secret['db']['username']);
define("DB_PASSWD", $secret['db']['password']);
define("DB_NAME", $secret['db']['name']);
define("DB_PREFIX", $secret['db']['prefix']);

// константы вк
define("VK_CLIENT_ID", $secret['vk']['clientId']); // идентификатор пользователя
define("VK_REDIRECT_URI", $secret['vk']['redirectUri']); // uri для редиректа
define("VK_SCOPE", $secret['vk']['scope']); // права доступа
define("VK_CLIENT_SECRET", $secret['vk']['clientSecret']); // секретный ключ приложения

// удаляем секретную инфу из памяти
unset($secret);