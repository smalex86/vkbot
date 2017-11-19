<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\Common\Controller\Component;

use Smalex86\Common\Controller;
use Smalex86\Common\Model\StaticComponentMapper;

/**
 * Description of Static
 *
 * @author Alexandr Smirnov
 */
class StaticController extends Controller {

  public function __construct($alias = '') {
    parent::__construct($alias);
  }
   
  protected function getRecord() {
    if (!$this->record) {
      $this->record = $this->getMapper()->getByAlias($this->getAlias());
    }
    return $this->record;
  }
  
  protected function getMapper() {
    if (!$this->mapper) {
      $this->mapper = new StaticComponentMapper;
    }
    return $this->mapper;
  }
  
  public function getBody() {
    if ($this->getRecord()) {
      $data = $this->getRecord()->text;
    } else {
      $data = 'Запрашиваемый компонент не найден';
    }
    return $data;
  }
  
  public function getTitle() {
    if ($this->getRecord()) {
      return $this->getRecord()->name;
    }
    return 'Компонент не найден';
  }
  
}
