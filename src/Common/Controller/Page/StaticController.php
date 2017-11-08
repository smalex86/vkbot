<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\Common\Controller\Page;

use Smalex86\Common\Controller;
use Smalex86\Common\Model\StaticPageMapper;

/**
 * Description of Static
 *
 * @author Alexandr Smirnov
 */
class StaticController extends Controller {
  
  public function __construct($alias = '') {
    parent::__construct($alias);
    $this->mapper = new StaticPageMapper;
    $this->record = $this->mapper->getByAlias($alias);
  }
   
  protected function getRecord() {
    if (!$this->record) {
      $this->record = $this->getMapper()->getByAlias($this->getAlias());
    }
    return $this->record;
  }
  
  protected function getMapper() {
    if (!$this->mapper) {
      $this->mapper = new StaticPageMapper;
    }
    return $this->mapper;
  }
  
  public function getBody() {
    if ($this->getRecord()) {
      $data = '<div class="page-header">';
      $data .= sprintf('<h1>%s</h1>', $this->getRecord()->pageName);
      $data .= '</div>';
      $data .= $this->getRecord()->pageText;
    } else {
      $data = 'Запрашиваемая страница не найдена';
    }
    return $data;
  }
  
  public function getTitle() {
    if ($this->getRecord()) {
      return $this->getRecord()->pageTitle;
    }
    return 'Страница не найдена';
  }
  
}
