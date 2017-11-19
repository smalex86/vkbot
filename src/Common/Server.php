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
  protected $menuControllers = array(); // массив контроллеров меню страницы
  protected $componentControllers = array(); // массив контроллеров компонентов страницы
  
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
   * Метод формирует действие текущей страницы по значениям параметров GET
   * В базовом варианте обрабатывается два варианта:
   *  1. Возвращается $_GET['action'] если он существует
   *  2. Возвращается 'view' если $_GET['action'] не существует
   * @return string
   */
  protected function getPageAction() {
    if (isset($_GET['action'])) {
      return $_GET['action'];
    } else {
      return 'view';
    }
  }

  /**
   * Метод выполняет поиск контроллера от типа, алиаса и действия
   * @param string $type
   * @param string $alias
   * @param string $action
   * @return Controller возвращается объект контроллера
   */
  protected function getController($type, $alias, $action = 'view') {
    // сначала выполняем поиск контроллеров с динамическим содержимым
    $controllerClassFinder = new ControllerFinder($this->getLogger(), $this->getDatabase());
    if (!$controllerClassFinder) {
      $this->logger->errorD(__FILE__.'('.__LINE__.'): Ошибка при создании объекта ControllerFinder');
      return null;
    }
    switch ($type) {
      case 'page':
        $className = $controllerClassFinder->getPageClass($alias, $action);
        break;
      case 'component':
        $className = $controllerClassFinder->getComponentClass($alias, $action);
        break;
      case 'menu':
        $className = $controllerClassFinder->getMenuClass($alias, $action); 
        break;
    }   
    // если такой класс в таблице контроллеров не зарегистрирован, то обращаемся к контроллеру со 
    // статическим содержимым
    if (!$className) {
      switch ($type) {
        case 'page':
          $className = 'Smalex86\\Common\\Controller\\Page\\StaticController';
          break;
        case 'component':
          $className = 'Smalex86\\Common\\Controller\\Component\\StaticController';
          break;
        case 'menu':
          $className = 'Smalex86\\Common\\Controller\\Menu\\StaticController';
          break;
      }
    }       
    if (class_exists($className)) {
      return new $className($alias);
    } else {
      $this->logger->errorD(__FILE__.'('.__LINE__.'): Файл с контроллером (type='.$type.
              ', alias='.$alias.') класса ' .$className.' не найден');
      return null;
    }
  }
  
  /**
   * Метод выполняет поиск и создание объекта pageController по параметрам строки GET
   * @return Controller
   */
  protected function getPageController() {
    if (!$this->controller) {
      $this->controller = $this->getController('page', $this->getPageAlias(), $this->getPageAction());
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
  
  /**
   * Универсальный метод для получения содержимого компонентов, меню и прочих объектов страницы
   * @param string $type тип компонента: component, menu
   * @param string $alias алиас требуемого компонента
   * @param array of string $pages обозначает на страницах с какими алиасами выводить компонент
   * @param boolean $inverse если true, то будет выводить компонент на всех страницах кроме $pages
   * @param int $position дополнительный параметр компонента
   * @return string
   */
  protected function getAnyComponent($type, $alias, $pages = array(), $inverse = false, $position = 0) {
    //проверить введен ли массив страниц
    if ($pages) {
      // если введен, то проверить не входит ли текущая страница в этот массив + inverse
      if (!(in_array($this->getPageAlias(), $pages) xor $inverse)) {
        return '';
      }
    }
    // если уже существует массив в контроллерами компонентов, выполнить поиск среди них
    $componentController = null;
    if (count($this->componentControllers)) {
      foreach ($this->componentControllers as $controller) {
        // если найден контроллер с таким алиасом, то запоминает его и выходим из цикла
        if ($controller->getAlias() == $alias) {
          $componentController = $controller;
          break;
        }
      }
    } 
    // если контроллер в массиве не найден, то вызываем поиск контроллера
    if (!$componentController) {
      $componentController = $this->getController($type, $alias);
      // если контроллер найден и создан, то добавляем его в массив контроллеров компонентов
      if ($componentController) {
        $this->componentControllers[] = $componentController;
      }
    }
    // проверяем найден ли контроллер, если нет - на выход, если да - запросить содержимое
    if ($componentController) {
      return $componentController->getBody();
    } else {
      return '';
    }
  }
  
  /**
   * Метод возвращает содержимое компонента
   * @param string $alias алиас требуемого компонента
   * @param array of string $pages обозначает на страницах с какими алиасами выводить компонент
   * @param boolean $inverse если true, то будет выводить компонент на всех страницах кроме $pages
   * @param int $position дополнительный параметр компонента
   * @return string
   */
  public function getComponent($alias, $pages = array(), $inverse = false, $position = 0) {
    return $this->getAnyComponent('component', $alias, $pages, $inverse, $position);
  }
  
  /**
   * Метод возвращает содержимое меню
   * @param string $alias алиас требуемого компонента
   * @param array of string $pages обозначает на страницах с какими алиасами выводить компонент
   * @param boolean $inverse если true, то будет выводить компонент на всех страницах кроме $pages
   * @return type
   */
  public function getMenu($alias, $pages = array(), $inverse = false) {
    return $this->getAnyComponent('menu', $alias, $pages, $inverse);
  }
  
  /**
   * Метод выполняет команды необходимые для запуска построения страницы.
   * Вынесен за пределы конструктора, потому что многие объекты при создании обращаются к 
   * объекту Server application, который уже должен быть создан на момент их вызова
   */
  public function startPageBuilder() {
    $this->getPageController(); // поиск и создание контроллера страниц
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
