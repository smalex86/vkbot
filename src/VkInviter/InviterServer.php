<?php
namespace Smalex86\VkInviter;

use Smalex86\Common\Server;
use Smalex86\VkInviter\VkAccountList;

/**
 * Описание класса сервера бота для вк
 * @author Alexandr Smirnov
 * @version 0.1
 *
 */
class InviterServer extends Server {

  private $token = '';
  
  private $accountList;
  
  public function __construct() {
    parent::__construct();
    $this->namespace = __NAMESPACE__;
    $this->getSession();
  }
  
  public function getAccountList() {
    if (!$this->accountList) {
      $this->accountList = new VkAccountList;
      if (!$this->accountList) {
        $msg = 'Не удалось обратиться к объекту VkAccountList';
        $this->getLogger()->errorD(__FILE__.':'.__LINE__.': '.$msg);
        return null;
      }
    }
    return $this->accountList;
  }  
  
  /**
   * метод для проверки существования токена
   * если токен на найден, то пользователю выдается ссылка, по которой нужно перейти
   * чтобы запросить разрешение у вк
   */
  public function checkToken() {
    // проверяем токен в базе
    require_once __DIR__ . '/include/vkTokenDatabase.php';
    if ($this->token = getToken()) {
      // если все хорошо, то возвращаем true
      return true;
    } else {
      // иначе возвращаем запрос на формирование нового токена
      // в виде алгоритма по шагам
      /*include 'vkTokenCodeLink.php';*/
      /*return getNewCodeQuery();*/
      include __DIR__ . '/include/vkTokenQueryForm.php';
      return getNewTokenForm();
    }
  }

  /**
   * метод получает ссылку на получение токена и выполняет редирект
   * @param string $code
   */
  public function setNewToken($code) {
    include 'vkTokenQueryLink.php';
    $link = '';
    if ($code) $link = getNewTokenQuery($code);
    setLogMsg(3, __FILE__ . ' (' . __LINE__ . ') -> ' . __METHOD__ . ' : ' . ' -- Ссылка: ' . $link);
    $json = file_get_contents($link);
    setLogMsg(3, __FILE__ . ' (' . __LINE__ . ') -> ' . __METHOD__ . ' : ' . ' -- Получен объект: ' . $json);
    // сохраняем данные токена в базе
    $obj = json_decode($json, true);
    require_once 'vkTokenDatabase.php';
    if (setToken($obj['access_token'], $obj['expires_in'])) {
      // выполняем редирект на страницу без параметра code
      $link = 'Location: ' . VK_REDIRECT_URI;
      header($link);
    } else {
      echo 'При сохранении токена возникла ошибка';
      return false;
    }
    return true;
  }

  public function getToken() {
    return $this->token;
  }

  public function initUserList() {
//    include 'vkUserListInit.php';
//    $rows = initUserList();
//    echo 'Добавлено строк = ' . $rows;
  }
	
}
