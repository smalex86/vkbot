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

use Smalex86\Common\{Logger, Session, Database};

/**
 * Description of Server
 *
 * @author Alexandr Smirnov
 */
class Server {
  
  protected $logger = null;
  protected $session = null;
  protected $database = null;
  
  /**
   * Возвращает объект логгера
   * @return Smalex86\Common\Logger
   */
  public function getLogger() {
    if (!$this->logger) {
      $this->logger = new Logger;
    }
    return $this->logger;
  }
  
  /**
   * Возвращает объект для работы с сессиями
   * @return Smalex86\Common\Session
   */
  public function getSession() {
    if (!$this->session) {
      $this->session = new Session($this->getLogger());
      if (!$this->session) {
        $msg = 'Не удалось обратиться к объекту Session';
        $this->getLogger()->errorD(__FILE__.':'.__LINE__.': '.$msg);
        return null;
      }
    }
    return $this->session;
  }
  
  /**
   * Возвращает объект соединения с базой данных, при создании объекта выполняется 
   * попытка подключения к базе данных
   * @return Smalex86\Common\Database
   */
  public function getDatabase() {
    if (!defined('DB_HOST') || !defined('DB_USERNAME') || !defined('DB_PASSWD') || !defined('DB_NAME')) {
      $msg = 'Не определены константы для подключения к базе данных';
      $this->getLogger()->errorD(__FILE__.':'.__LINE__.': '.$msg);
      return null;
    }
    if (!$this->database) {
      $this->database = new Database($this->getLogger(), DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
      if (!$this->database) {
        $msg = 'Не удалось обратиться к объекту базы данных';
        $this->getLogger()->errorD(__FILE__.':'.__LINE__.': '.$msg);
        return null;
      }
    }
    return $this->database;
  }
  
}