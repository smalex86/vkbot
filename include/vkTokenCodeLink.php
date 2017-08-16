<?php

global $botServer;
if (!isset($botServer)) die("Не обнаружен головной объект приложения");

/**
 * Функция формирует HTML-ссылку для получения прав доступа к объектам вк для получения токена
 * @return unknown
 */
function getNewCodeQuery() {
	// если строка с token не найдена или expired_date уже истек, то делаем запрос нового token
	// для этого выводим ссылку для перехода пользователем для создания нового токена
	return sprintf('<a href="https://oauth.vk.com/authorize?client_id=%s&display=page&redirect_uri=%s&scope=gruops&responce_type=code&v=5.68">Необходимо получить новый токен</a>',
			VK_CLIENT_ID, VK_REDIRECT_URI);
}