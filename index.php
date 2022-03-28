<?php
header('Content-type:text/html,charset=utf-8');
mb_detect_order(
    [
        'UTF-8',
        'ASCII'
    ]
);

require 'vendor/autoload.php';

!defined('ROOT_PATH') && define('ROOT_PATH', __DIR__);
!defined('SRC_PATH') && define('SRC_PATH', ROOT_PATH . '/src');
!defined('STATIC_PATH') && define('STATIC_PATH', SRC_PATH . '/static');
!defined('JS_PATH') && define('JS_PATH', STATIC_PATH . '/js');
!defined('PHP_PATH') && define('PHP_PATH', STATIC_PATH . '/php');
!defined('JSON_PATH') && define('JSON_PATH', STATIC_PATH . '/json');
!defined('POSTAL_PATH') && define('POSTAL_PATH', STATIC_PATH . '/postal');

spl_autoload_register(function ($class) {
    $path = ROOT_PATH . '/' . preg_replace('/Magein\\\ChinaDistrict/', 'src', $class) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

//$postal = new \Magein\ChinaDistrict\Postal();
//$postal->make();
//$postal->move();
//die();


$gaode = new \Magein\ChinaDistrict\Platform\GaoDe();
$gaode->makeDistrict();
$gaode->makeDistrictCode();
$gaode->makeDistrictLevel();
$gaode->makeFramework();
$gaode->makeJson();









