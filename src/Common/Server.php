<?php

/*
 * This file is part of the Smalex86 package.
 *
 * (c) Alexandr Smirnov <mail_er@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smalex86\Common;

use Smalex86\Common\{Logger, Session, Database, ControllerFinder};

/**
 * Description of Server
 *
 * @author Alexandr Smirnov
 */
class Server {
  
  protected $logger = null; // поле для хранения указателя на объект логгера
  protected $session = null; // поле для хранения указателя на объект сессии
  protected $database = null; // поле для хранения указателя на объект для работы с базой данных
  protected $controller = null; // controller текущей страницы, из него осуществляется доступ к странице
  
  protected $namespace = null; // хранит namespace, поле необходимо чтобы после 
                               // переопределения методов корректно работали ссылки
  
  public function __construct() {
    $this->namespace = __NAMESPACE__;
    $this->getLogger();
  }
  
  /**
   * Возвращает объект логгера
   * @return Smalex86\Common\Logger
   */
  public function getLogger() {
    if (!$this->logger) {
      $this->logger = new Logger;
    }
    return $this->logger;
  }
  
  /**
   * Возвращает объект для работы с сессиями
   * @return Smalex86\Common\Session
   */
  public function getSession() {
    if (!$this->session) {
      $this->session = new Session($this->getLogger());
      if (!$this->session) {
        $msg = 'Не удалось обратиться к объекту Session';
        $this->getLogger()->errorD(__FILE__.':'.__LINE__.': '.$msg);
        return null;
      }
    }
    return $this->session;
  }
  
  /**
   * Возвращает объект соединения с базой данных, при создании объекта выполняется 
   * попытка подключения к базе данных
   * @return Smalex86\Common\Database
   */
  public function getDatabase() {
    if (!$this->database) {
      if (!defined('DB_HOST') || !defined('DB_USERNAME') || !defined('DB_PASSWD') || !defined('DB_NAME')) {
        $msg = 'Не определены константы для подключения к базе данных';
        $this->getLogger()->errorD(__FILE__.':'.__LINE__.': '.$msg);
        return null;
      }
      $this->database = new Database($this->getLogger(), DB_HOST, DB_USERNAME, DB_PASSWD, DB_NAME);
      if (!$this->database) {
        $msg = 'Не удалось обратиться к объекту базы данных';
        $this->getLogger()->errorD(__FILE__.':'.__LINE__.': '.$msg);
        return null;
      }
    }
    return $this->database;
  }
  
  /**
   * Метод формирует алиас текущей страницы по значениям параметров GET
   * В базовом варианте обрабатывается два варианта:
   *  1. Возвращается $_GET['page'] если он существует
   *  2. Возвращается 'main' если $_GET['page'] не существует
   * @return string
   */
  protected function getPageAlias() {
    if (isset($_GET['page'])) {
      return $_GET['page'];
    } else {
      return 'main';
    }
  }

  /**
   * Метод выполняет поиск и создание объекта pageController по параметрам строки GET
   * @return Controller
   */
  public function getPageController() {
    if (!$this->controller) {
      $controllerClassFinder = new ControllerFinder($this->getLogger(), $this->getDatabase());
      $className = $controllerClassFinder->getPageClass($this->getPageAlias());
      if (class_exists($className)) {
        $this->controller = new $className;
      } else {
        $this->logger->errorD(__FILE__.'('.__LINE__.'): Файл с контроллером класса '.$className.' не найден');
        return null;
      }
    } 
    return $this->controller;
  }
  
  /**
   * Возвращает заголовок страницы
   * @return string
   */
  public function getPageTitle() {
    return $this->getPageController()->getTitle() . ' - ' . ST_NAME;
  }
  
  /**
   * Возвращает содержимое страницы
   * @return string
   */
  public function getPageContent() {
    return $this->getPageController()->getBody();
  }
  
  public function getComponent() {
    return '';
  }
  
  public function getMenu() {
    return '';
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
          $className = $this->namespace . '\\' . $field;
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
    // проверка на наличие данных оставленных после неудачной пост обработки
    $this->getSession()->checkPostData(); 
    return FALSE;
  }
  
}
