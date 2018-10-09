<?php

function curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
        [
            'Cookie: bdshare_firstime=1538210187711',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.12 Safari/537.36'
        ]
    );

    $data = curl_exec($ch);

    curl_close($ch);

    if (empty($data)) {
        exit('请求出错');
    }

    return mb_convert_encoding($data, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
}

function getProvinceId()
{
    $url = 'http://www.ip138.com/post/';

    $data = curl($url);

    preg_match_all('/href="\/([0-9a-z]+)\/"/', $data, $matches);

    $provinces = isset($matches[1]) ? $matches[1] : [];

    if (empty($provinces)) {
        exit('没有匹配到省份');
    }

    return $provinces;
}

function getProvinceCode($provinceID)
{
    $url = 'http://www.ip138.com';

    $data = curl($url . '/' . $provinceID . '/');

    preg_match_all('/<td>.*?<\/td>/', $data, $matches);

    $result = [];

    if (isset($matches[0])) {

        /**
         * 每三个为一组
         */
        $areas = array_chunk($matches[0], 3);

        foreach ($areas as $area) {
            $name = trim(preg_replace('/[\w<>\/="]+/', '$1', $area[0]));

            /**
             * 过滤掉空的和带括号的,一些行政区已经修改，用括号标记原来的名称，这里只保留最新的行政区域
             */
            if (empty($name) || preg_match('/（/', $name)) {
                continue;
            }

            preg_match('/[0-9]+/', $area[1], $matches);
            $postal = isset($matches[0]) ? $matches[0] : '';

            preg_match('/[0-9]+/', $area[2], $matches);
            $telCode = isset($matches[0]) ? $matches[0] : '';

            if ($name && $postal && $telCode) {
                $result[] = [
                    'name' => trim($name),
                    'postalCode' => $postal,
                    'telCode' => $telCode
                ];
            }
        }
    }

    return $result;
}

$provinces = getProvinceId();

$result = [];
if ($provinces) {
    foreach ($provinces as $province) {
        $result = array_merge($result, getProvinceCode($province));
    }
}

$telCode = [];
$postalCode = [];
if ($result) {
    foreach ($result as $item) {
        $telCode[$item['name']] = $item['telCode'];
        $postalCode[$item['name']] = $item['postalCode'];
    }
}

file_put_contents('./code/teleCode.php', var_export($telCode, true));
file_put_contents('./code/postalCode.php', var_export($postalCode, true));



