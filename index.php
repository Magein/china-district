<?php
//header('Content-type:text/html,charset=utf8');
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

/**
 * 代码执行顺序
 * 1. 生成district的code表（src/static/php/DistrictCode）
 * 2. 抓取邮编、区号
 */

// 获取地区的行政区划代码
$gaode = new \Magein\ChinaDistrict\Platform\GaoDe();
$gaode->makeDistrictCode();

// 抓取邮编、区号
$postal = new \Magein\ChinaDistrict\Postal();
$postal->make();
// 移动到src/static/php目录下，生成Postals.php、Tels.php
$postal->move();

// 生成地区表，包含名称、行政区划、邮编、区号
$gaode->makeDistrict();
// 生成层级结构的文件
$gaode->makeDistrictLevel();
// 前段使用的文件
$gaode->makeFramework();
// json文件
$gaode->makeJson();









