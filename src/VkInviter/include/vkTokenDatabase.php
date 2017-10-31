<?php

namespace Smalex86\VkInviter;

use Smalex86\Common\Logger;

global $inviterServer;
if (!isset($inviterServer)) {
  die("Не обнаружен головной объект приложения");
}

/**
 * Функция для проверки существования токена в таблице и истечения его срока
 * Если токен не найден или истек, то возвращается null
 * @return string|NULL
 */
function getToken() {
  global $inviterServer;
  $accessToken = '';
  // значение token хранится в таблице settings в строке token
  // получаем значение token из таблицы
  $query = sprintf('select * from %ssetting_list where name = "access_token" limit 1', DB_PREFIX);
  Logger::toLog(0, __FILE__ . ' (' . __LINE__ . ') : query = ' . $query);
  $row = null;
  if ($result = $inviterServer->getDB()->mysqli->query($query)) {
    $row = $result->fetch_assoc();
    Logger::toLog(3, __FILE__ . ' : ' . __LINE__ . ' -- result = ' . var_export($row, true));
    $result->close();
  } else {
    Logger::toLog(3, __FILE__ . ' : ' . __LINE__ . ' -- result error = ' . $inviterServer->getDB()->mysqli->error);
  }
  // если значение было обнаружено
  if ($row) {
    // сравниваем expired_date c текущей датой
    if (time() < $row['expired_date']) {
      // если токен еще действителен, то записываем его в переменную
      $accessToken = $row['value'];
    }
  }
  // возвращаем полученное значение 
  return $accessToken;
}

/**
 * Функция сохраняет значение нового токена и время когда он станет недействительным
 * @param unknown $token
 * @param unknown $expires_in
 * @return unknown|boolean
 */
function setToken($token, $expires_in) {
  global $botServer;
  $db = $botServer->getDB();
  $token = $db->real_escape_string($token);
  // вычисляем время истечения токена
  $expire_date = time() + $expires_in;
  $query = sprintf("insert into %ssetting_list (name, value, expired_date) "
          . "values ('access_token', '%s', %u) on duplicate key update value='%s', expired_date=%u", 
          DB_PREFIX, $token, $expire_date, $token, $expire_date);
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
