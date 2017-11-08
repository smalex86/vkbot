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

/**
 * Description of StaticPageMapper
 *
 * @author Александр
 */
class StaticPageMapper extends DataMapper {
  
   /**
   * метод возвращает название таблицы данных
   */
  protected function getTableName() {
    return 'page';
  }
  
  /**
   * возвращает список полей таблицы
   */
  protected function getFields() {
    return array('pid', 'psid', 'pagealias', 'pagelink', 'pagetitle', 'pagename', 'pageteaser', 
        'pagetext', 'public_date', 'published');
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
    $query = sprintf('select * from %s where pagealias = "%s" limit 1', $this->getTableName(), $alias);
    $row = $this->database->selectSingleRow($query, __FILE__.':'.__LINE__);
    if ($row) {
      return StaticPage::newRecord($row['pid'], $row['psid'], $row['pagealias'], $row['pagelink'], 
              $row['pagetitle'], $row['pagename'], $row['pageteaser'], $row['pagetext'], 
              $row['public_date'], $row['published']);
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
