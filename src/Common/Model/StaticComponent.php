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
 * Description of StaticComponent
 *
 * @author Alexandr Smirnov
 */
class StaticComponent extends ActiveRecord {
  
  public $comid;
  public $name;
  public $text;
  public $filename;
  
  /**
   * Данный статический метод создает экземпляр данного класса с указанными параметрами
   * @param int $comid
   * @param string $name
   * @param string $text
   * @param string $filename
   * @return \Smalex86\Common\Model\StaticComponent
   */
  static public function newRecord($comid, $name, $text, $filename) {
    $record = new StaticComponent;
    $record->comid = $comid;
    $record->name = $name;
    $record->text = $text;
    $record->filename = $filename;
    return $record;
  }
  
}
