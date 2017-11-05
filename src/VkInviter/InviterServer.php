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
    $this->logger = $this->getLogger();
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
   * Метод для обработки пост-данных
   * @return boolean
   */
  public function startActionManager() {
    if ($_POST) {
      foreach ($_POST as $field => $value) {
        $this->logger->debugD(__FILE__.'('.__LINE__.'): Данные = '.var_export($value, true));
        if (is_array($value)) {
          // подключение требуемой библиотеки
          $className = __NAMESPACE__ . '\\' . $field;
          $this->logger->debugD(__FILE__.'('.__LINE__.'): Класс = '.$className);
          $this->logger->debugD(__FILE__.'('.__LINE__.'): class exists = '.class_exists($className)); 
          if (class_exists($className)) {
            $obj = new $className;
            if ($obj && method_exists($obj, 'processAction')) {
              $obj->processAction($value);
            } else {
              $this->logger->warningD(__FILE__.'('.__LINE__.'): Класс '.$className.
                      ' не имеет метода processAction, данные ('.var_export($value, true).
                      ') не будут обработаны');
            }
          } else {
            $this->logger->warningD(__FILE__.'('.__LINE__.'): Класс '.$className.
                    ' не найден, данные ('.var_export($value, true).
                    ') не будут обработаны');
          }
        }
      }
      // чтобы снова не вызывался обработчик массива пост, очищаем его
      header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
    return FALSE;
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
