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

/**
 * Description of BasicObject
 *
 * @author Alexandr Smirnov
 */
class BasicObject {
  
  // файл логгирования
  private $logFileName = __CLASS__;
  
  /**
   * 
   * @param type $logFilename
   */
  public function setLogfile($logFilename) {
    $this->logFileName = $logFilename;
    return true;
  }
  
  public function toLog($msgStatus, $msg) {
    //include_once 'logger.php';
    return toLog($msgStatus, $msg, $this->logFileName);
  }
  
}
