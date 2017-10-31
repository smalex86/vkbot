<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\VkInviter;

use Smalex86\Common\BasicActiveRecord;

/**
 * Description of VkAccount
 *
 * @author Александр
 */
class VkAccount extends BasicActiveRecord {
  
  public $vkaid = 0;
  public $password = '';
  public $accessToken = '';
  public $expireDate = 0;
  
  /**
   * Данный статический метод создает экземпляр данного класса с указанными параметрами
   * @param int $vkaid
   * @param string $password
   * @param string $accessToken
   * @param int $expireDate
   * @return \Smalex86\VkInviter\VkAccount
   */
  static public function newAccount($vkaid, $password, $accessToken, $expireDate) {
    $account = new VkAccount;
    $account->vkaid = $vkaid;
    $account->password = $password;
    $account->accessToken = $accessToken;
    $account->expireDate = $expireDate;
    return $account;
  }
  
}
