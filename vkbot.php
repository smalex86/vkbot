<?php

ini_set('display_errors', 'On'); // показывать ошибки - на готовом продукте убрать

header('Content-Type: text/html; charset=utf-8');
include_once dirname(__FILE__) . '/lib/config.php';
include_once 'logging.php';

setLogMsg(3, __FILE__ . ' : ' . __LINE__ . ' -- POST = ' . var_export($_POST, true), 'getpost.log');
setLogMsg(3, __FILE__ . ' : ' . __LINE__ . ' -- GET = ' . var_export($_GET, true), 'getpost.log');
$content = file_get_contents("php://input");
setLogMsg(3, __FILE__ . ' : ' . __LINE__ . ' -- file_get_contents = ' . var_export($content, true), 'getpost.log');

include_once 'botServer.php';
$botServer = new botServer();



/* --------------------------------------- Работа с токеном -------------- */

// анализируем get запрос
if (isset($_GET['code'])) {
	var_dump($_GET['code']);
	// если пришел запрос на формирование токена
	// то формируем ссылку на получение токена и выполняем по ней переход
	$botServer->setNewToken($_GET['code']);
}

if (($checkToken = $botServer->checkToken()) !== true) {
	// проблема с токеном, делаем запрос на получение нового
	echo $checkToken . " \n";
}

/* --------------------------------------- Работа с токеном (конец) ------ */

// init
if (isset($_GET['init'])) {
	$botServer->initUserList();
}


// команда "выдать 20 ссылок на страницы новых пользователей"
if (isset($_GET['get20users'])) {
	// получить 
}


echo "<p>I am working. </p>\n";
if ($botServer->getToken()) {
	echo "<p>I've got working token! </p>\n";
	echo "<p><a href='vkbot.php?get20users'>Получить список ссылок на профили 20 новых пользователей</a></p>";
}
?>