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
use Smalex86\Common\Database;
use Smalex86\Common\Session;

/**
 * Description of DataMapper
 *
 * @author Alexandr Smirnov
 */
abstract class DataMapper {
  
  protected $logger; // объект логгирования
  protected $database; // объект бд
  protected $session; // объект для работы с сессией
  
  public function __construct() {
    global $application;
    $this->logger = $application->getLogger();
    $this->database = $application->getDatabase();
    $this->session = $application->getSession();
  }
  
  /**
   * метод возвращает название таблицы данных
   */
  abstract protected function getTableName();
  
  /**
   * возвращает список полей таблицы
   */
  abstract protected function getFields();
  
  /**
   * метод, выполняемый перед вставкой в бд
   */
  abstract protected function beforeInsert();
  
  /**
   * возвращает объект по идентификатору
   */
  abstract public function getById($id);
  
  /**
   * возвращает список объектов
   */
  abstract public function getList();
  
  /**
   * выполняет сохранение объекта в бд
   */
  abstract public function save($obj);
  
  /**
   * выполняет обработку пост-данных
   */
  abstract public function processAction($postData = array());
  
}
