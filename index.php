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

// Get route
$route = explode('/', $_SERVER['REQUEST_URI']);

// Set default route
if(count($route) < 3)
	$route = 'home';		
else
	$route = $route[2];

// Get availible routes
$routes = array();
$controllers = glob(CONTROLLERPATH.'*.*');
foreach($controllers as $controller) {
	$filename = pathinfo($controller)['filename'];
	$routes[$filename] = pathinfo($controller)['basename'];
}

/*
 * ------------------------------------------------------
 *  File Loader
 * ------------------------------------------------------
 */

class Loader {
	public static $files = array();

	public static function load_class($filepath, $rootpath = null) {

		$file_exists = isset(Loader::$files[$filepath]);

		if(!$file_exists) {
			Loader::$files[$filepath] = $filepath;
			$rootpath = is_null($rootpath) ? APPPATH : $rootpath; 
			include($rootpath.$filepath);
		}
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

include(APPPATH.'models/nav.php');

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

	public function loadModel($filename) {
		Loader::load_class($filename);
	}

	public function loadView($view) {
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

// Access controller for route
include(CONTROLLERPATH.$routes[$route]);

/*
 * ------------------------------------------------------
 *  Load Page with Controller
 * ------------------------------------------------------
 */

$controller = $route."controller";
$controller = new $controller;
$controller->index();




