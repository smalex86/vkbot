<?php

/*
 * This file is part of the Smalex86\Common package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\Common;

use Smalex86\Common\Logger;

/**
 * Description of Database
 *
 * @author Alexandr Smirnov
 */
class Database {
  
  public $errno = 0; // код ошибки
  public $errstr = ''; // текст ошибки
  public $mysqli = null; // ссылка на объект класса mysqli	
  private $logger = null;

  /**
   * конструктор класса
   * реализует внутри себя подключение к бд и проверку на ошибки
   */
  function __construct(Logger $logger, $host, $username, $password, $name) {
    $this->logger = $logger;
    $this->mysqli = new \mysqli($host, $username, $password, $name);
    if ($this->mysqli->connect_error) {
      $this->errno = $this->mysqli->connect_errno;
      $this->errstr = $this->mysqli->connect_error;
      $msg = 'Connection error (' . $this->errno . '): ' . $this->errstr;
      $this->logger->ErrorD($msg);
      die($msg);
    }  
    else if (!$this->mysqli->set_charset('utf8')) {
      $this->logger->WarningD("Ошибка при загрузке набора символов utf8: %s\n", 
              $this->mysqli->error);
    }
  }
  
  public function getLastError() {
    return sprintf('(%u) %s', $this->mysqli->errno, $this->mysqli->error);
  }

  /**
   * Метод для получения объекта mysqli с установленным соединением
   * @return mysqli|boolean
   */
  function getMysql() {
    if (($this->mysqli) && (!$this->errno)) {
      return $this->mysqli;
    }
    return false;
  }

  /**
   * заглушка для функции fetch_all
   * @param unknown $result
   * @return NULL|unknown
   */
  function fetchAll($result) {
    $data = null;
    while($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
    return $data;
  }
  
  /**
   * Выполняет обработку строки для обезопасивания перед вставкой в sql запрос
   * @param string $str
   * @return string
   */
  public function getSafetyString($str) {
    return $this->mysqli->real_escape_string($str);
  }
  
  /**
   * Метод выполняет обращение к базе данных, результатом которого является единственная строка
   * @param string $query запрос
   * @param string $place место, из которого он вызывается
   * @return array
   */
  public function selectSingleRow($query, $place) {
    return $this->queryProcess('singleSelect', $query, $place);
  }
  
  /**
   * Метод выполняет обращение к базе данных, результатом которого является массив строк
   * @param string $query запрос
   * @param string $place место, из которого он вызывается
   * @return array of array
   */
  public function selectMultipleRows($query, $place) {
    return $this->queryProcess('multipleSelect', $query, $place);
  }
  
  public function insertSingle($query, $place) {
    $insertId = $this->queryProcess('singleInsert', $query, $place);
    if ($insertId) {
      $msg = $place.': insert_id = '.$insertId;
      $this->logger->debugD($msg);
    }
    return $insertId;
  }
  
  /**
   * Метод обрабатывает запрос к базе данных с указанием в каком виде данные вернуть
   * @param string $queryType тип возвращаемых данных 
   * @param string $query запрос
   * @param string $place место, из которого вызывается запрос
   * @return array|array of array|null
   */
  private function queryProcess($queryType, $query, $place) {
    $msg = $place.': query = '.$query;
    $this->logger->debugD($msg);
    if (!$query) {
      return null;
    }
    if ($result = $this->mysqli->query($query)) {
      return $this->queryResultProcess($queryType, $result);
    } else {
      $msg = $place.': Ошибка при выполнении запроса ('.$this->mysqli->errno.'): '
              .$this->mysqli->error;
      $this->logger->errorD($msg);
      return null;
    }
  }
  
  /**
   * Метод обрабатывает результат работы запроса чтобы получить данные в виде массива
   * @param string $queryType
   * @param mysqlResult $result
   * @return array|array of array
   */
  private function queryResultProcess($queryType, $result) {
    switch ($queryType) {
      case 'singleSelect':
        $row = $result->fetch_assoc();
        $result->close();
        return $row;
        break;
      case 'multipleSelect':
        $rows = $this->fetchAll($result);
        $result->close();
        return $rows;
        break;
      case 'singleInsert':
        return $this->mysqli->insert_id; // при добавлении одной записи возвращаем ее новый ид
        break;
      default:
        break;
    }
  }
  
}
