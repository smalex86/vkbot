<?php

/**
 * Description of BasicObject
 *
 * @author Александр
 */
class BasicObject {
  
  private $logFileName = '';
  
  public function __construct($logFileName = __CLASS__) {
    $this->logFileName = $logFileName;
  }
  
  public function toLog($msgStatus, $msg) {
    include_once 'logging.php';
    return toLog($msgStatus, $msg, $this->logFileName);
  }
  
}
