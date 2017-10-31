<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\Common;

use Smalex86\Common\Logger;
use Smalex86\Common\BasicFunctionObject as Functions;

/**
 * Description of Session
 *
 * @author Alexandr Smirnov
 */
class Session {
  
  protected $logger;

  public function __construct(Logger $logger) {
    $this->logger = $logger;
  }
  
  /**
   * Проверка сессии на существование массива postData (данные, сохраненные после 
   * выполнения post-обработчика при ошибке обработки). 
   * Если пользователь ушел со страницы postData[url], то эти данные удаляются
   * 
   * @return boolean
   */
  public function checkPostData() {
    if (isset($_SESSION['postData']) && is_array($_SESSION['postData'])) {
      // проверка данных
      foreach ($_SESSION['postData'] as $field => $value) {
        if (!isset($value['url']) || !isset($value['data']) || empty($value['data'])) {
          unset($_SESSION['postData'][$field]);
          continue;
        }
        if ($value['url'] != Functions::getCurrentUrl()) {
          // если пользователь ушел со страницы $_SESSION['postData'][$field]['url'], то удаляем этот массив
          unset($_SESSION['postData'][$field]);
        }
      }
    }
    return true;
  }

  /**
   * Возвращает данные сохраненные после неудачной пост-обработки
   * 
   * @return array
   */
  public function getPostData() {
    if (isset($_SESSION['postData']) && is_array($_SESSION['postData'])) {
      return $_SESSION['postData'];
    } else {
      return array();
    }
  }

  /**
   * Возвращает структуру данных, сохраненную после неудачной 
   * пост-обработки для текущего адреса страницы
   * 
   * @return array
   */
  public function getPostDataForCurrentUrl() {
    $postData = $this->getPostData();
    if (!empty($postData)) {
      foreach ($postData as $value) {
        if ($value['url'] == Functions::getCurrentUrl()) {
          return $value['data'];
        }
      }
    }
    return array();
  }

  /**
   * Очищает пост-данные с текущей страницы
   * 
   * @return boolean
   */
  public function unsetPostData() {
    if (isset($_SESSION['postData']) && !empty($_SESSION['postData'])) {
      foreach ($_SESSION['postData'] as $field => $value) {
        if ($value['url'] == Functions::getCurrentUrl()) {
          unset($_SESSION['postData'][$field]);
        }
      }
    }
    return true;
  }
  
  /**
   * Сохраняет пост-данные для текущей страницы
   * 
   * @param array $data
   * @return boolean
   */
  public function addPostData(array $data = array()) {
    if (!empty($data)) {
      $this->unsetPostData(); // очистить данные с такой же страницы
      $postData['url'] = Functions::getCurrentUrl();
      $postData['data'] = $data;
      $_SESSION['postData'][] = $postData;
      return true;
    }
    return false;
  }

  /**
   * Проверяет, формирует и выводит список пост-сообщений.
   * Если пользователь ушел со страницы postMsg[url], то эти сообщения удаляются
   * 
   * @return string
   */
  public function checkPostMsg() {
    // проверка сессии на существование массива postMsg (результат выполнения post-обработчика)
    $postMsg = '';
    $postMsgScript = '';
    if (isset($_SESSION['postMsg']) && is_array($_SESSION['postMsg'])) {
      // вывод сообщений
      foreach ($_SESSION['postMsg'] as $field => $value) {
        if (!isset($value['url']) || !isset($value['msg']) || empty($value['msg'])) {
          unset($_SESSION['postData'][$field]);
          continue;
        }
        if ($value['url'] == Functions::getCurrentUrl()) {
          // если url текущей страницы = $value['url'], то выводим сообщение
          $postMsg .= $this->getMsgHTMLbyMsg($value['msg'], @$value['status'], $field);
        } else {
          // если пользователь ушел со страницы $_SESSION['postMsg'][$field]['url'], то удаляем этот массив
          unset($_SESSION['postMsg'][$field]);
        }
      }
      // скрипт для отработки удаления ошибок
      if ($postMsg) {
        $postMsgScript = $this->getMsgScript();
      }
    }
    if (empty($_SESSION['postMsg'])) {
      unset($_SESSION['postMsg']);
    }
    // вывод сообщения из post-обработчика
    return $postMsg . $postMsgScript;
  }
  
  /**
   * сохранение сообщений о выполнении обработчиков post
   * @param string $url
   * @param string $msg
   * @param int $status
   */
  public function setPostMessageToSession($msg, $status, $url = '') {
    // в сессию записать адрес страницы и сообщение
    // когда пользователь уйдет с данной страницы надо будет удалить массив postMsg - 
    // данная проверка выполняется при выводе страницы
    if ($msg) {
      $url = ($url) ? $url : Functions::getCurrentUrl();
      // проверка на наличие такого же сообщения в массиве session
      $add = 1;
      if (is_array(@$_SESSION['postMsg'])) {
        foreach ($_SESSION['postMsg'] as $field => $value) {
          if ($value['url'] == $url && $value['msg'] == $msg && $value['status'] == $status) {
            $add = 0;
            break;
          }
        }
      }
      // если такого сообщения в массиве сообщений не найдено, то добавляем текущее
      if ($add) {
        $obj['url'] = $url;
        $obj['msg'] = $msg;
        $obj['status'] = $status;
        $_SESSION['postMsg'][] = $obj;
      }
    }
  }

  /**
   * удаление сообщений о выполнении обработчиков post
   * @param int $msgId
   */
  public function delPostMessageFromSession($msgId) {
    if (isset($_SESSION['postMsg'][$msgId])) {
      unset($_SESSION['postMsg'][$msgId]);
    }
  }
  
  /**
   * возвращает сообщение в зависимости от msg и status
   * @param string $msg
   * @param int $status
   * @param int $id
   * @return string
   */
  private function getMsgHTMLbyMsg($msg, $status, $id = 0) {
    if ($msg) {
      $data = sprintf("<div class='alert %s'>", ($status == 1) ? 'alert-success' : 'alert-danger');
      $data .= sprintf('<button type="button" class="close" data-dismiss="alert" msgId="%u">'
              . '<span aria-hidden="true">&times;</span>'
              . '<span class="sr-only">Close</span></button>', $id);
      $data .= sprintf('<p>%s</p>', $msg);
      $data .= "</div>";
      return $data;
    }
    return '';
  }

  /**
   * скрипт для удаления сообщений из очереди на вывод
   * @return string
   */
  private function getMsgScript() {
    $data = "<script type='text/javascript'>$(function () {";
      /* найти все элементы для отправки данных на сервер */
      $data .= "$('button[msgId]').click( function() {"
                  . "var posting = $.post('ajax.php?object=Smalex86\Common\Session&action=delPostMsgById&msgId='"
                  . "+ $(this).attr('msgId'));"
                . "});";
    // закрывающий хвост 				
    $data .= "});</script>";
    return $data;
  }
  
  /**
   * обработчик действий ajax
   * @param array $ajaxData
   * @return boolean
   */
  public function processAjaxAction($ajaxData = array()) {
    $this->logger->debugD(__FILE__.'('.__LINE__.'): ajax begin');
    // анализ массива _GET		
    if ($ajaxData) {
      switch ($ajaxData['action']) {
        case 'delPostMsgById':
          if (isset($ajaxData['msgId'])) {
            $this->delPostMessageFromSession($ajaxData['msgId']);
          }
        break;	
      }
    }
    return false;
  }
  
}
