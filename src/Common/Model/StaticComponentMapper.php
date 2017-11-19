<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\Common\Model;

use Smalex86\Common\DataMapper;
use Smalex86\Common\Model\StaticComponent;

/**
 * Description of StaticComponentMapper
 *
 * @author Александр
 */
class StaticComponentMapper extends DataMapper {
  
   /**
   * метод возвращает название таблицы данных
   */
  protected function getTableName() {
    return 'component';
  }
  
  /**
   * возвращает список полей таблицы
   */
  protected function getFields() {
    return array();
  }
  
  /**
   * метод, выполняемый перед вставкой в бд
   */
  protected function beforeInsert() {
    
  }
  
  /**
   * возвращает объект по идентификатору
   */
  public function getById($id) {
    
  }
  
  public function getByAlias($alias) {
    $alias = $this->database->getSafetyString($alias);
    $query = sprintf('select * from %s where name = "%s" limit 1', $this->getTableName(), $alias);
    $row = $this->database->selectSingleRow($query, __FILE__.':'.__LINE__);
    if ($row) {
      return StaticComponent::newRecord($row['comid'], $row['name'], $row['text'], $row['filename']);
    }
    return null;
  }
  
  /**
   * возвращает список объектов
   */
  public function getList() {
    
  }
  
  /**
   * выполняет сохранение объекта в бд
   */
  public function save($obj) {
    
  }
  
  /**
   * выполняет обработку пост-данных
   */
  public function processAction($postData = array()) {
    
  }
  
}
