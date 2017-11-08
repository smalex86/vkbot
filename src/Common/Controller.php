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
  protected $record = null;
  
  protected $alias = '';

  public function __construct($alias = '') {
    global $application;
    $this->logger = $application->getLogger();
    if ($alias) {
      $this->alias = $alias;
    }
  }
  
  /**
   * Метод возвращает алиас контроллера
   */
  public function getAlias() {
    return $this->alias;
  }

  /**
   * Метод возвращает DataMapper контроллера
   */
  abstract protected function getMapper();

  /**
   * Метод возвращает ActiveRecord контроллера
   */
  abstract protected function getRecord();


  /**
   * Метод возвращающий заголовок страницы\компонента\меню, который не входит в состав body
   */
  abstract public function getTitle();
  
  /**
   * Метод возращающий содержимое
   */
  abstract public function getBody();
  
}
