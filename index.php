<?php
namespace Smalex86\VkInviter;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/config.php';

use Smalex86\Common\Logger;

Logger::setLogFolder(__DIR__ . '/logs/');
$logger = new Logger();

$logger->toLogD(3, __FILE__ . ' : ' . __LINE__ . ' -- POST = ' . var_export($_POST, true), 
        'getpost.log');
$logger->toLogD(3, __FILE__ . ' : ' . __LINE__ . ' -- GET = ' . var_export($_GET, true), 
        'getpost.log');
$logger->toLogD(3, __FILE__ . ' : ' . __LINE__ . ' -- file_get_contents = ' 
        . var_export(file_get_contents("php://input"), true), 'getpost.log');

$application = new inviterServer(); // создание сервера
$application->startActionManager(); // запуск обработки пост-данных
$application->startPageBuilder(); // запуск построения страницы

//include('templates/main/index.php');
include('templates/dashboard/index.php');

/* --------------------------------------- Работа с токеном -------------- */

// анализируем get запрос
//if (isset($_GET['code'])) {
//	var_dump($_GET['code']);
//	// если пришел запрос на формирование токена
//	// то формируем ссылку на получение токена и выполняем по ней переход
//	$inviterServer->setNewToken($_GET['code']);
//}

//if (($checkToken = $inviterServer->checkToken()) !== true) {
//	// проблема с токеном, делаем запрос на получение нового
//	//echo $checkToken . " \n";
//}

/* --------------------------------------- Работа с токеном (конец) ------ */

// init
//if (isset($_GET['init'])) {
//	$botServer->initUserList();
//}


// команда "выдать 20 ссылок на страницы новых пользователей"
//if (isset($_GET['get20users'])) {
//	// получить 
//}


//echo "<p>I am working. </p>\n";
//if ($inviterServer->getToken()) {
	//echo "<p>I've got working token! </p>\n";
	//echo "<p><a href='vkbot.php?get20users'>Получить список ссылок на профили 20 новых пользователей</a></p>";
//}