<?php

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . '/application');
define('YONETIM_MEDIA_URL','/admin/assets');
define('DATA_URL', BASE_PATH.'/public/assets/data/');
define('PANEL_ADI','CMS Yönetim Paneli');
define('SITE_ADI','CMS');
define('ADMIN_YETKI_KODU','superadmin');
define('MAX_ROW', 5);
define('SITE_MEDIA_URL','/site/assets');
define('SITE_DESC','Description Deneme');
define('SITE_KEYWORDS','Description Deneme');

date_default_timezone_set('Europe/Istanbul');

//error_reporting(E_ALL || ~E_NOTICE);
error_reporting(E_ALL);
mb_internal_encoding("UTF-8");

set_include_path('.'
    . PATH_SEPARATOR . ROOT_DIR . '/library'
    . PATH_SEPARATOR . ROOT_DIR . '/application/models'
    . PATH_SEPARATOR . ROOT_DIR . '/application/modules'
    . PATH_SEPARATOR . get_include_path());


// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV',
(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
    : 'production'));


require_once 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

// Zend_Application
require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$frontendOptions = array(
    'automatic_serialization' => true
);

$backendOptions  = array(
    'cache_dir'=> '/var/tmp'
);

$cache = Zend_Cache::factory('Core',
    'File',
    $frontendOptions,
    $backendOptions);

Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

Zend_Layout::startMvc(array('layoutPath' => ROOT_DIR . '/application/layouts'));

$application->bootstrap();
$application->run();