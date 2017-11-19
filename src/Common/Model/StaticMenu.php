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

use Smalex86\Common\ActiveRecord;

/**
 * StaticMenu Active Record
 *
 * @author Alexandr Smirnov
 */
class StaticMenu extends ActiveRecord {
  
  public $mid;
  public $name;
  public $alias;
  public $template;
  public $type;
  
  protected $items = array();


  /**
   * Данный статический метод создает экземпляр данного класса с указанными параметрами
   * @param int $mid
   * @param string $name
   * @param string $alias
   * @param string $template
   * @param string $type
   * @param array $items
   * @return \Smalex86\Common\Model\StaticComponent
   */
  static public function newRecord($mid, $name, $alias, $template, $type, $items) {
    $record = new StaticMenu;
    $record->mid = $mid;
    $record->name = $name;
    $record->alias = $alias;
    $record->template = $template;
    $record->type = $type;
    $record->items = $items;
    return $record;
  }
  
  private $itemsTree = array(); // массив дерева элементов меню
        
  /**
   * Метод производит сортировку массива в виде дерева + добавляет атрибут childs
   * Сохраняет данные рекурсивным проходом в $itemsTree
   * @param array $items
   * @param int $miid
   * @return int
   */
  private function getTreeItemsMenu($items, $miid = 0) {
    $childs = 0;
    foreach ($items as $item) {
      if ($item['parent_miid'] == $miid) {
        $this->itemsTree[] = $item;
        $childs++;
        $this->itemsTree[count($this->itemsTree)-1]['childs'] = $this->getTreeItemsMenu($items, $item['miid']);
      }
    }
    return $childs;
  }
  
  /**
   * Выролняет поиск верхнего предка (используется при поиске активного пункта меню)
   * @param array $items массив элементов меню
   * @param int $parent_miid
   * @return int идентификатор предка
   */
  private function getParentItemMenu($items, $parent_miid) {
    $miid = 0;
    foreach ($items as $item) {
      if ($item['miid'] == $parent_miid) {   
        $miid = $item['miid'];
        if ($item['parent_miid']) {
          if (!$miid2 = $this->getParentItemMenu($items, $item['parent_miid'])) { $miid = $miid2; }
        }
        break;
      }
    }
    return $miid;
  }

  /**
   * метод для получения идентификаторы активного пункта меню верхнего уровня
   * @param type $items
   * @return type
   */
  private function getActiveItem($items) {
    $miid = 0;
    $active = false;
    foreach ($items as $item) {
      if (($item['itemlink']) && (strpos($_SERVER['REQUEST_URI'], $item['itemlink'], 0))) {
        // в случае если активный пункт меню вложенный, то установить active у корневого пункта
        $active = true;
        if ($item['parent_miid']) {
          $miid = $this->getParentItemMenu($items, $item['parent_miid']);
        } else {
          $miid = $item['miid'];
          break;
        }    
      }
    }
    // если пользователь находиться на главной, то присвоить пункту меню mainpage значение класса active
    if (!$active) {
      foreach ($items as $item) {
        if ($item['itemalias'] == 'mainpage') {
          $miid = $item['miid'];
        }
      }
    }            
    return $miid;
  }
  
  /**
   * построение пунктов меню
   * @param array $items сортированный список пунктов меню
   * @param int $miidActive идентификатор активного пункта меню
   * @param int $miid идентификатор пункта меню, с которого начинается построение (для рекурсии)
   * @return string
   */
  private function getTreeItemsMenuHTML($items, $miidActive, $miid = 0) {
    if (!$miid) {
      switch ($this->type) {
        case 'nav':
          $data = '<ul class="nav nav-pills nav-stacked">';    
          break;
        case 'navbar':
          $data = '<ul class="nav navbar-nav">';    
          break;
      }
    } else {
      $data = '<ul class="dropdown-menu">';
    }
    foreach ($items as $item) {
      if ($item['parent_miid'] == $miid) {
        $data .= sprintf('<li class="%s%s%s">', ($item['miid'] == $miidActive) ? ' current active' : '',
          (!$item['childs'] == 0) ? ' dropdown parent' : '', ($item['itemname'] == '-') ? ' divider': '');
        if ($item['itemname'] <> '-') {
          $data .= sprintf('<a%s href="%s">%s%s</a>', (!$item['childs'] == 0) ? ' class="dropdown-toggle" data-toggle="dropdown"' : '',
            ($item['itemlink']) ? $item['itemlink'] : 'index.php', $item['itemname'], (!$item['childs'] == 0) ? '<b class="caret"></b>' : '');
        }
        if ($item['childs'] > 0) {
          $data .= $this->getTreeItemsMenuHTML($items, $miidActive, $item['miid']);
        }
        $data .= "</li>";
      }
    }
    $data .= '</ul>';
    return $data;
  }

  // функция формирования меню
  public function getMenu() {
    $data = null;
    if (is_array($this->items)) {
      $this->getTreeItemsMenu($this->items); // сортировка списка ссылок и назначение атрибута childs
      $miidActive = $this->getActiveItem($this->itemsTree); // поиск активной ссылки
      $data = $this->getTreeItemsMenuHTML($this->itemsTree, $miidActive); // построение списка меню
    }
    return $data;
  }
  
}
