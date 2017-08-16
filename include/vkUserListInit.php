<?php

global $botServer;
if (!isset($botServer)) die("Не обнаружен головной объект приложения");


/**
 * Функция читает массив пользователей из файла и записывает в таблицу
 * @param unknown $token
 * @param unknown $expires_in
 * @return unknown|boolean
 */
function initUserList() {
	global $botServer;
	$db = $botServer->getDB();
	
	$fileName = dirname(dirname(__FILE__)) . '/json.txt';
	$json = json_decode(file_get_contents($fileName), true);
	$json = $json['userList'];
	
	$query = sprintf("insert into %suser_list (vk_uid, send_message_date) values ", DB_PREFIX);
	$obj = null;
	foreach ($json as $value) {
		$obj[] = "(" . $value['id'] . ", " . strtotime($value['date']) . ")";
	}
	$obj = implode(', ', $obj);
	$query .= $obj;
	setLogMsg(0, __FILE__ . ' (' . __LINE__ . ') : query = ' . $query);
	if ($result = $db->query($query)) {
		$affectedRows = $db->affected_rows;
		setLogMsg(0, __FILE__ . ' : ' . __LINE__ . ' -- affectedRows = ' . $affectedRows);
		if (!$affectedRows) {
			setLogMsg(2, __FILE__ . ' : ' . __LINE__ . ' -- result error = ' . $db->error);
		}
		return $affectedRows;
	} else {
		setLogMsg(1, __FILE__ . ' : ' . __LINE__ . ' -- result error = ' . $db->error);
		return false;
	}
}


