<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\VkInviter\Controller\Page;

use Smalex86\Common\Controller;
use Smalex86\VkInviter\Model\VkAccountMapper;

/**
 * Description of VkAccount
 *
 * @author Alexandr Smirnov
 */
class VkAccount extends Controller {
  
  public function __construct($alias = '') {
    parent::__construct($alias);
  }
  
  protected function getRecord() {
//    if (!$this->record) {
//      $this->record = $this->getMapper()->getByAlias($this->getAlias());
//    }
//    return $this->record;
  }
  
  protected function getMapper() {
    if (!$this->mapper) {
      $this->mapper = new VkAccountMapper();
    }
    return $this->mapper;
  }
  
  /**
   * Формирование списка подключенных аккаунтов в вк в виде html таблицы
   * @return string
   */
  public function getAccountListHtml() {
    $data = '';
    $list = $this->getMapper()->getList();
    if ($list) {
      $data .= '<table class="table table-bordered">';
      $data .= '<tr>';
        $data .= '<th>vkaid</th>';
        $data .= '<th>password</th>';
        $data .= '<th>accessToken</th>';
        $data .= '<th>expireDate</th>';
      $data .= '</tr>';
      foreach ($list as $value) {
        $data .= '<tr>';
        $data .= '<td>' . $value->vkaid . '</td>';
        $data .= '<td>' . $value->password . '</td>';
        $data .= '<td>' . $value->accessToken . '</td>';
        $data .= '<td>' . $value->expireDate . '</td>';
        $data .= '</tr>';
      }
      $data .= '</table>';
    } else {
      $data .= '<p>Не найдены подключенные аккаунты</p>';
    }
    return $data;
  }
  
  /**
   * Формирование формы для подключения нового аккаунта вк в виде html
   * @return string
   */
  public function getAccountAddFormHtml() {
    $data = '<form method="post">';
    $data .= '<ol>';
    $data .= '<li>Войдите в подключаемый аккаунт во ВКонтакте</li>';
    $data .= sprintf('<li>Нажмите на кнопку: <a href="%s" class="btn btn-default" target="_blank">ссылка</a></li>', 
            $this->getNewCodeQuery());
    $data .= '<li>Вставьте содержимое атрибута code из полученного адреса: '
            . '<input type="text" id="inputTextCode"> <a id="updateTextCode" class="btn btn-default">ok</a></li>';
    $data .= sprintf('<li>Нажмите на кнопку: <a href="%s" class="btn btn-default" target="_blank" id="accessTokenQueryLink">ссылка</a></li>',
            $this->getNewTokenQuery(''));
    $data .= '<li>Вставьте полученные значения <br />'
            . 'access_token: <input type="text" id="inputAccessToken" name="VkAccountMapper[add][token]"><br />'
            . 'expires_in: <input type="text" name="VkAccountMapper[add][expiresIn]"><br />'
            . 'user_id: <input type="text" name="VkAccountMapper[add][userId]">'
            . '<button id="addAccessToken" class="btn btn-success" name="VkAccountMapper[add][submit]" type="submit">ok</button></li>';
    $data .= '</ol>';
    $data .= '</form>';
    $data .= '<script type="text/javascript">'
            . 'var changeQueryLink = function() {'
            . '  var val = document.getElementById("inputTextCode").value,'
            . '    aQuery = document.getElementById("accessTokenQueryLink"),'
            . '    href = aQuery.getAttribute("href");'
            . '    aQuery.setAttribute("href", href.substring(0,href.indexOf("code=")) + "code=" + val);'
            . '};'
            . 'document.getElementById("updateTextCode").addEventListener("click", changeQueryLink);'
            . '</script>';
    return $data;
  }
  
  /**
   * Функция формирует HTML-ссылку для получения прав доступа к объектам вк для получения токена
   * @return unknown
   */
  private function getNewCodeQuery() {
    // если строка с token не найдена или expired_date уже истек, то делаем запрос нового token
    // для этого выводим ссылку для перехода пользователем для создания нового токена
    return sprintf('https://oauth.vk.com/authorize?client_id=%s&display=page&'
            . 'redirect_uri=%s&scope=%s&responce_type=code&v=5.68',
            VK_CLIENT_ID, VK_REDIRECT_URI, VK_SCOPE);
  }

  /**
   * Функция формирует ссылку для получения токена
   * @param string $code
   * @return string
   */
  function getNewTokenQuery($code) {
    return sprintf('https://oauth.vk.com/access_token?client_id=%s&client_secret=%s&'
            . 'redirect_uri=%s&code=%s', VK_CLIENT_ID, VK_CLIENT_SECRET, VK_REDIRECT_URI, $code);
  }
  
  public function getTitle() {
    return 'Аккаунты ВКонтакте';
  }
  
  public function getBody() {
    $data = '<h3>Подключенные аккаунты</h3>';
    $data .= $this->getAccountListHtml();
    $data .= '<h3>Подключение аккаунта</h3>';
    $data .= $this->getAccountAddFormHtml();
    return $data;
  }
  
}
