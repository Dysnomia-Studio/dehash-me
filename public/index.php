<?php
session_start();

define('DIR_CTRL', '..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR);
define('DIR_ERRORS', '..'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR);
define('DIR_LIB', '..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR);
define('DIR_LANG', '..'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR);
define('DIR_VIEW', '..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR);
define('DIR_MODEL', '..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR);
define('FILE_CONFIG', '..'.DIRECTORY_SEPARATOR.'config.php');

require_once DIR_CTRL.'autoload.php';

include_once(FILE_CONFIG);

$lang = new Language(); // Init Translation system

define('DIR_CACHE', '..'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$lang->getLanguage().'-');

$currentPage = (isset($_GET['route']) && $_GET['route'] != "")?$_GET['route']:"index";
$currentPage = htmlentities(htmlspecialchars($currentPage));
$currentPage = explode(".", $currentPage)[0];

$router = new Router();
$pageData = $router->get($currentPage);

include DIR_VIEW.'mainstruct.php';