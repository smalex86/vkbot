<?php

namespace Smalex86\Common;
/**
 * 
 * Описание класса объекта для подключения к субд mysqli
 * Требует наличия констант DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME
 *
 * @author Smirnov Alexandr
 * @version 1.1
 * 
 */
class Database {

  public $errno = 0; // код ошибки
  public $errstr = ''; // текст ошибки
  private $mysqli = null; // ссылка на объект класса mysqli	


  /**
   * конструктор класса
   * реализует внутри себя подключение к бд и проверку на ошибки
   */
  function __construct() {
    $this->mysqli = new \mysqli(DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
    if ($this->mysqli->connect_error) {
      $this->errno = $this->mysqli->connect_errno;
      $this->errstr = $this->mysqli->connect_error;
      die('Connection error (' . $this->errno . '): ' . $this->errstr);
    }  
    else if (!$this->mysqli->set_charset('utf8')) {
      printf("Ошибка при загрузке набора символов utf8: %s\n", $this->mysqli->error);
    }
  }

  /**
   * Метод для получения объекта mysqli с установленным соединением
   * @return mysqli|boolean
   */
  function getMysqli() {
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
  function fetch_all($result) {
    $data = null;
    while($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
    return $data;
  }
	
}
