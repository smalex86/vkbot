<?php
namespace Smalex86\VkInviter;

use Smalex86\Common\Logger;

/**
 * Описание класса сервера бота для вк
 * @author Alexandr Smirnov
 * @version 0.1
 *
 */
class InviterServer {

  private $db = null; // объект базы данных mysqli
  private $token = '';

  function __construct() {
    //require_once 'database.php';
    $db = new \Smalex86\Common\Database;
    $this->db = $db->getMysqli();
    if (!$this->db) {
      Logger::toLog(1, __FILE__ . ' (' . __LINE__ . ') -> ' . __METHOD__ . ' : ' . ' -- Не удалось обратиться к объекту базы данных');
      die('Не удалось обратиться к объекту базы данных');
    }
    Logger::toLog(3, __FILE__ . ' (' . __LINE__ . ') -> ' . __METHOD__ . ' : ' . ' -- Объект создан');
  }

  /**
   * метод-обертка для передачи ссылки на объект mysqli
   * @return mysqli|boolean
   */
  public function getDB() {
    return $this->db;
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

?>