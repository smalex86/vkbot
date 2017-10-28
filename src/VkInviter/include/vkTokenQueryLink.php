<?php

global $botServer;
if (!isset($botServer)) die("Не обнаружен головной объект приложения");

/**
 * Функция формирует ссылку для получения токена
 * @param string $code
 * @return unknown
 */
function getNewTokenQuery($code) {
	$query = sprintf('https://oauth.vk.com/access_token?client_id=%s&client_secret=%s&redirect_uri=%s&code=%s',
			VK_CLIENT_ID, VK_CLIENT_SECRET, VK_REDIRECT_URI, $code);
	return $query;
}