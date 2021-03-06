<?php

/**
 * Simple-MVC
 *
 * A simple php MVC framework created for BYU MRI Facility
 *
 * @package Simple-MVC
 * @author Michael Mendoza
 */
	
define('Simple-MVC_VERSION', '0.0.1');

/**
 *---------------------------------------------------------------
 * APP and API Directory Names
 *---------------------------------------------------------------
 */

$app_path = 'app';
$api_path = 'api';

/**
 *---------------------------------------------------------------
 * Application Paths
 *---------------------------------------------------------------
 */

define('BASEURL','http://192.168.33.12/');
define('BASEPATH','http://192.168.33.12/');
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('APPPATH', $app_path.DIRECTORY_SEPARATOR);
define('APIPATH', $api_path.DIRECTORY_SEPARATOR);
define('CONTROLLERPATH', APPPATH.'controllers'.DIRECTORY_SEPARATOR);
define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);
define('LAYOUTPATH', VIEWPATH.'layouts'.DIRECTORY_SEPARATOR);
define('PAGESPATH', VIEWPATH.'pages'.DIRECTORY_SEPARATOR);
define('COMPONENTSPATH', VIEWPATH.'components'.DIRECTORY_SEPARATOR);
define('ASSETSPATH', APPPATH.'assets'.DIRECTORY_SEPARATOR);

/**
 *---------------------------------------------------------------
 * Application DB Varibles
 *---------------------------------------------------------------
 */

define('MYSQL_HOST','localhost'); 
define('MYSQL_USERNAME','root'); 
define('MYSQL_PASSWORD','root'); 
define('MYSQL_DBNAME','mriFacilityDB');

/**
 *---------------------------------------------------------------
 * Session Varibles
 *---------------------------------------------------------------
 */

if(!isset($_SESSION)) {
	session_start();
}

/*
 * ------------------------------------------------------
 *  Route from URL
 * ------------------------------------------------------
 */

class Router {

	private $uri;
	private $route;
	private $routes;	
	private $action;

	function __construct() {

		// Get parse uri
		$this->uri = explode('/', $_SERVER['REQUEST_URI']);

		// Set action
		$this->action = null;
		if(count($this->uri) > 2)
			$this->action = $this->uri[2];

		// Set route
		if($this->uri[1] == '')
			$this->route = 'home';		
		else
			$this->route = $this->uri[1];

		// Get availible routes
		$this->routes = array();
		$controllers = glob(CONTROLLERPATH.'*.*');
		foreach($controllers as $controller) {
			$filename = pathinfo($controller)['filename'];
			$this->routes[$filename] = pathinfo($controller)['basename'];
		}
	}

	function goToRoute() {

		// Access controller for route
		include(CONTROLLERPATH.$this->routes[$this->route]);

		// Load
		$controller = $this->route."controller";
		$controller = new $controller;

		$action = $this->action;
		if(is_null($this->action))
			$controller->index();
		else
			$controller->$action();
	}
}

/*
 * ------------------------------------------------------
 *  File Importer
 * ------------------------------------------------------
 */

/**
 * import - loads files, and saves a list of class names loaded
 */
function import($class, $file, $path = null) {
	static $_classes = array();

	$class = strtolower($class);
	$class_exists = isset($_classes[$class]);

	if(!$class_exists) {
		$_classes[$class] = $class;
		$path = is_null($path) ? APPPATH : $path;
		include_once($path.$file);
	}
}

/*
 * ------------------------------------------------------
 *  Nav Components
 * ------------------------------------------------------
 */

class NavLink {
	public $name;
	public $link;

	function __construct($name, $link_or_links) {
		$this->name = $name;
		$this->link = $link_or_links;
	}

	public function isLink() {
		return !is_array($this->link);
	}

	public function render() { 
		if($this->isLink()) {
			echo "<li><a href='{$this->link}'>{$this->name}</a></li>";
		}
		else {
			echo "<li><a href=''>{$this->name}</a>";
			echo "<ul>";
			foreach($this->link as $sublink)
				$sublink->render();
			echo "</ul>";
			echo "</li>";
		}
	}
}

/*
 * ------------------------------------------------------
 *  Get Controller from Route
 * ------------------------------------------------------
 */

class Controller
{
	public $header = 'header';
	public $footer = 'footer';
	public $layout = 'layout';

	function __construct() {
		include_once(APPPATH.'models/nav.php');
	}

	public function loadModel($class, $file, $param = null) {
		import($class, $file);
		$instance = isset($param) ? new $class($param) : new $class();
		return $instance;
	}

	public function loadView($view, $data = null) {
		// Get paths
		$headerpath = LAYOUTPATH.$this->header.'.php';
		$pagepath = PAGESPATH.$view.'.php';
		$footerpath = LAYOUTPATH.$this->footer.'.php';		
		$layoutpath = LAYOUTPATH.$this->layout.'.php';

		// Get model data for layouts
		$nav = new NavModel;
		$nav_links = $nav->getNavLinks();

		// Render layout
		include($layoutpath);
	}
}

$router = new Router();
$router->goToRoute();












