<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\VkInviter;

use Smalex86\Common\BasicDataMapper;
use Smalex86\VkInviter\VkAccount;

/**
 * Description of VkAccountMapper
 *
 * @author Александр
 */
class VkAccountMapper extends BasicDataMapper {
  
  protected function getTableName() {
    return 'vk_account';
  }
  
  protected function getFields() {
    return array('vkaid', 'password', 'access_token', 'expire_date');
  }

  /**
   * Выдает объект по идентификатору
   * @param int $id
   * @return Smalex86\VkInviter\VkAccount
   */
  public function getById($id) {
    if (!is_numeric($id)) {
      $msg = __FILE__.':'.__LINE__.': Некорректно указан идентификатор объекта ('.$id.')';
      $this->logger->warningD($msg);
      return null;
    }
    $query = sprintf('select * from %s where vkaid = %u limit 1', $this->getTableName(), $id);
    $row = $this->database->selectSingleRow($query, __FILE__.':'.__LINE__);
    if ($row) {
      return VkAccount::newAccount($row['vkaid'], $row['password'], $row['access_token'], 
                $row['expire_date']);
    }
    return null;
  }
  
  /**
   * Выдает список объектов подключенных аккаунтов вк
   * @return array of Smalex86\VkInviter\VkAccount
   */
  public function getList() {
    $query = \sprintf('select * from %s', $this->getTableName());
    $rows = $this->database->selectMultipleRows($query, __FILE__.':'.__LINE__);
    if ($rows) {
      $list = array();
      if ($rows) {
        foreach ($rows as $value) {
          $list[] = VkAccount::newAccount($value['vkaid'], $value['password'], 
                  $value['access_token'], $value['expire_date']);
        }
      }
      return $list;
    }
    return null;
  }
  
  protected function beforeInsert() {
    return true;
  }
  
  public function save($obj) {
    return true;
  }
  
  public function processAction($postData = array()) {
    if (is_array($postData)) {      
      if (isset($postData['add'])) {
        // добавление нового аккаунта через данные из формы
        $result = $this->addByData($postData['add']);
        $this->session->setPostMessageToSession($result['message'], $result['status']);
      }
    }
    return false;
  }
  
  protected function beforeInsertByData($data) {
    $this->logger->debugD(__FILE__.'('.__LINE__.') data = '.var_export($data, true));
    if (!empty($data['token'])) {
      $data['token'] = $this->database->getMysql()->real_escape_string($data['token']);
    }
    if (!is_numeric($data['expiresIn'])) {
      $data['expiresIn'] = 0;
    }
    if (!is_numeric($data['userId'])) {
      return array();
    }
    return $data;
  }
  
  protected function addByData($data) {
    $data = $this->beforeInsertByData($data);
    $result['status'] = 0;
    $result['message'] = 'Не удалось добавить новый аккаунт вк';
    if ($data) {
      $query = sprintf('insert into %s (%s) values (%u, "%s", "%s", %u)', 
              $this->getTableName(), implode(',', $this->getFields()), $data['userId'], '', $data['token'], 
              ($data['expiresIn']) ? time() + $data['expiresIn'] : $data['expiresIn']);
      $insertId = $this->database->insertSingle($query, __FILE__.':'.__LINE__);
      if ($insertId) {
        $result['status'] = 1;
        $result['message'] = 'Добавлен новый аккаунт вк с идентификатором '.$data['vkaid'];
      } else {
        $result['message'] .= '. Ошибка: '.$this->database->getLastError();
      }
    }
    return $result;
  }
  
}
