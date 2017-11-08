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
 * Description of StaticPage
 *
 * @author Alexandr Smirnov
 */
class StaticPage extends ActiveRecord {
  
  public $pid;
  public $psid;
  public $pageAlias;
  public $pageLink;
  public $pageTitle;
  public $pageName;
  public $pageTeaser;
  public $pageText;
  public $publicDate;
  public $published;
  
  /**
   * Данный статический метод создает экземпляр данного класса с указанными параметрами
   * @param type $pid
   * @param type $psid
   * @param type $pageAlias
   * @param type $pageLink
   * @param type $pageTitle
   * @param type $pageName
   * @param type $pageTeaser
   * @param type $pageText
   * @param type $publicDate
   * @param type $published
   * @return \Smalex86\Common\Model\StaticPage
   */
  static public function newRecord($pid, $psid, $pageAlias, $pageLink, $pageTitle, $pageName, 
          $pageTeaser, $pageText, $publicDate, $published) {
    $record = new StaticPage;
    $record->pid = $pid;
    $record->psid = $psid;
    $record->pageAlias = $pageAlias;
    $record->pageLink = $pageLink;
    $record->pageTitle = $pageTitle;
    $record->pageName = $pageName;
    $record->pageTeaser = $pageTeaser;
    $record->pageText = $pageText;
    $record->publicDate = $publicDate;
    $record->published = $published;
    return $record;
  }
  
  
  
}
