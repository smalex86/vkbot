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

/**
 * Класс для логгирования информации
 * @author Alexandr Smirnov <mail_er@mail.ru>
 * @version 1.3
 */
class Logger {

  // определяет ведется ли логирование или нет
  //статус логирования: 
  //4 - запись всего, 
  //3 - отладка, 
  //2 - запись ошибок и предупреждений, 
  //1 - запись только ошибок, 
  //0 - логгирование не ведется
  private static $status = 4; 
  private static $logfileDefault = 'common.log';
  private static $folder = __DIR__ . '/logs/';

  /**
   * Функция логгирования.
   * Ведет запись логов в файл, указанный в параметре $logFileName
   * @param int $msgStatus 0 - необходимая инфа, 1 - error, 2 - warning, 3 - debug, 4.. - другое
   * @param string $msg строка сообщения для записи в лог-файл
   * @param string $logFileName имя лог-файла, указывается только имя файла без пути, 
   * лог-файлы хранятся в корневой папке logs
   * @return boolean
   */
  public static function toLog($msgStatus, $msg, $logFileName = '') {
    // проверка на необходимость записи данного сообщения
    // определяется текущим статусом логгирования и статусом сообщения
    if (self::$status < $msgStatus) {
      return false;
    }
    // если запись нужно двигаемся дальше
    if (!$logFileName) {
      $logFileName = self::$logfileDefault;
    }
    switch ($msgStatus) {
      case 0: $msgStatusWord = 'IMPORTANT INFO';
        break;
      case 1: $msgStatusWord = 'ERROR';
        break;
      case 2: $msgStatusWord = 'WARNING';
        break;
      case 3: $msgStatusWord = 'DEBUG INFO';
        break;
      default: $msgStatusWord = 'UNKNOWN INFO';
    }
    // записываем сообщение в файл
    return file_put_contents(self::$folder . $logFileName, 
            sprintf('%s :: %s :: %s' . PHP_EOL, date("Y-m-d H-i-s"), $msgStatusWord, $msg), 
            FILE_APPEND);
  }
  
  /**
   * Метод для установки значения статуса (уровня) логгирования
   * @param type $status
   * @return boolean
   */
  public static function setStatus($status) {
    self::$status = $status;
    return true;
  }
  
  /**
   * Метод для установки значения папки для логфайлов
   * @param type $folder
   * @return boolean
   */
  public static function setLogFolder($folder) {
    self::$folder = $folder;
    return true;
  }
  
  /**
   * Возвращает директорию для сохранения логфайлов
   * @return type
   */
  public static function getLogFolder() {
    return self::$folder;
  }

}

// setLogMsg(2, __FILE__ . ' : ' . __LINE__ . ' -- access denied. ', 'lib.log');
// setLogMsg(0, __FILE__ . ' : ' . __LINE__ . ' -- query = ' . $query, 'lib.log');
// setLogMsg(0, __FILE__ . ' : ' . __LINE__ . ' -- affectedRows = ' . $affectedRows, 'lib.log');
// setLogMsg(1, __FILE__ . ' : ' . __LINE__ . ' -- addError = ' . $this->db->error, 'lib.log');