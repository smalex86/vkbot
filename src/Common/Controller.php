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
 * Description of Controller
 *
 * @author Alexandr Smirnov
 */
abstract class Controller {
  
  protected $logger = null;
  protected $mapper = null;

  public function __construct() {
    global $application;
    $this->logger = $application->getLogger();
  }
  
  /**
   * Метод возвращающий заголовок страницы\компонента\меню, который не входит в состав body
   */
  abstract public function getTitle();
  
  /**
   * Метод возращающий содержимое
   */
  abstract public function getBody();
  
}
