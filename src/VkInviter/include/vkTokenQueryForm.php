<?php

namespace Smalex86\VkInviter;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $inviterServer;
if (!isset($inviterServer)) {
  die("Не обнаружен головной объект приложения");
}

/**
 * Функция формирует HTML-ссылку для получения прав доступа к объектам вк для получения токена
 * @return unknown
 */
function getNewCodeQuery() {
  // если строка с token не найдена или expired_date уже истек, то делаем запрос нового token
  // для этого выводим ссылку для перехода пользователем для создания нового токена
  return sprintf('<a href="https://oauth.vk.com/authorize?client_id=%s&display=page&redirect_uri=%s&scope=%s&responce_type=code&v=5.68" target="_blank">Пройдите по ссылке для получения значения Code</a>', VK_CLIENT_ID, VK_REDIRECT_URI, VK_SCOPE);
}

/**
 * Функция формирует ссылку для получения токена
 * @param string $code
 * @return unknown
 */
function getNewTokenQuery($code) {
  $query = sprintf('https://oauth.vk.com/access_token?client_id=%s&client_secret=%s&redirect_uri=%s&code=%s', VK_CLIENT_ID, VK_CLIENT_SECRET, VK_REDIRECT_URI, $code);
  return $query;
}

function getNewTokenForm() {
  $data = '';
  // 1. Ссылка на формирование Code
  $data .= sprintf('<p>1. %s</p>', getNewCodeQuery());
  // 2. Поле для вставки нового Code
  $input = '<input type="text" id="inputTextCode">';
  $data .= sprintf('<div>2. Вставьте содержимое атрибута code: %s</div>', $input);
  // 3. Скрипт, срабатывающий после вставки в поле, который изменяет ссылку на формирование токена
  $data .= '<script type="text/javascript">'
          . 'var changeInputCodeText = function(e) { console.log(e); };'
          . 'var inputCode = document.getElementById("inputTextCode");'
          . 'inputCode.addEventListener("change", changeDiagram);'
          . '</script>';
  // 4. Ссылка на получение токена
  //$data .= sprintf('<p>2. %s</p>', getNewCodeQuery());
  // 5. Форма с полем для вставки полученного токена и кнопкой для его передачи в post-запросе серверу
  return $data;
}
