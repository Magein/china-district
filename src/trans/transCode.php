<?php

/**
 * 获取中国市级区号(新疆维吾尔自治区各县)
 * @return array
 */
function areaCode()
{
    $areaCode = [];

    // 数据来源
    $source = 'http://www.knowsky.com/tools/toolsdianhuaquhaoduizhaobiao.asp';

    $content = file_get_contents($source);

    $content = iconv('gb2312', 'utf-8', $content);

    preg_match('/<td valign="top">[\s\S]+新疆维吾尔自治区各县/', $content, $matches);

    if (isset($matches[0])) {

        preg_match_all('/[\s\S]{1,20} [0-9]{1,5}/', $matches[0], $matches);

        if (isset($matches[0])) {
            $matches = $matches[0];
            foreach ($matches as $item) {
                $item = trim(preg_replace('/<|>|td|\/|b/', '', $item));
                $item = explode(' ', trim($item));
                if (isset($item[0]) && isset($item[1])) {
                    $areaCode[$item[0]] = $item[1];
                }
            }
        }
    }

    preg_match('/<b>乌鲁木齐市<\/b><\/td>[\s\S]+831300/', $content, $matches);

    if (isset($matches[0])) {
        preg_match_all('/<td.+>[<b>]?(.+)[<\/b>]?<\/td>/', $matches[0], $matches);
        if ($matches[1]) {
            $matches = $matches[1];
            array_unshift($matches, '乌鲁木齐市');
            foreach ($matches as $key => $item) {
                $item = preg_replace('/<\/b>|&nbsp;/', '', $item);
                if ($item) {
                    if (!preg_match('/^[\x4e00-\x9fa5]+$/', $item)) {
                        if ($matches[$key + 1]) {
                            $areaCode[$item] = $matches[$key + 1];
                        }
                    }
                }
            }
        }
    }

    return $areaCode;
}

/**
 * 邮政编码数据转化
 * @return array
 */
function postalCode()
{
    $postalCode = [];

    // 数据来源：百度文库
    $source = 'https://baike.baidu.com/item/邮政编码/725269?fr=aladdin&fromid=4857444&fromtitle=邮编';

    $content = file_get_contents($source);

    preg_match('/邮政编码<\/span>直辖市<\/h3>[\s\S]+国外邮编/', $content, $matches);

    if (isset($matches[0])) {

        $matches = $matches[0];

        $result = preg_replace_callback('/<a.{1,100}>([\S]{1,20})<\/a>/', function ($matches) {
            return $matches[1];
        }, $matches);

        preg_match_all('/para">(.+[0-9]{6})<\/div>/', $result, $matches);

        if (isset($matches[1])) {

            $matches = $matches[1];

            foreach ($matches as $item) {

                $result = preg_replace_callback('/([0-9]{6})/', function ($matches) {
                    return ' ' . $matches[1] . ' ';
                }, $item);

                $result = array_values(array_filter(explode(' ', $result)));

                foreach ($result as $key => $value) {

                    $value = trim(preg_replace('/<|>|b|\//', '', $value));

                    if (preg_match('/^[0-9]{6}$/', $value)) {
                        continue;
                    }

                    $code = isset($result[$key + 1]) ? $result[$key + 1] : '';

                    if ($code && preg_match('/^[0-9]{6}$/', $code)) {
                        $postalCode[$value] = $code;
                    }
                }
            }
        }
    }

    return $postalCode;
}