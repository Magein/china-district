<?php

namespace Magein\ChinaDistrict\Platform;

use Magein\ChinaDistrict\Platform;
use Magein\ChinaDistrict\Write;
use Overtrue\Pinyin\Pinyin;

class GaoDe extends Platform
{
    public function name()
    {
        return 'gaode';
    }

    protected function path()
    {
        return ROOT_PATH . '/src/Platform/Gaode/';
    }

    protected function filePath($ext = 'json', $year = '')
    {
        if (empty($year)) {
            $year = date('Ymd');
        }

        return $this->path() . "$year.$ext";
    }

    public function district()
    {
        $path = $this->filePath();
        if (is_file($path)) {
            return json_decode(file_get_contents($path), true);
        }

        $env = file_get_contents(ROOT_PATH . '/.env');
        preg_match('/key=(.*)/', $env, $matches);
        $key = $matches[1] ?? '';

        if (!$key) {
            return [];
        }

        $params = [
            'key' => $key,
            'keywords' => '中国',
            'subdistrict' => '3',
            'extensions' => 'base',
        ];

        $url = 'https://restapi.amap.com/v3/config/district?';

        $url .= urldecode(http_build_query($params));

        $json = file_get_contents($url);

        $data = json_decode($json, true);

        $res = [];
        if ($data['status'] ?? 0 == 1) {
            $res = $data['districts'][0]['districts'] ?? [];
        }

        file_put_contents($path, json_encode($res, JSON_UNESCAPED_UNICODE));

        return $res;
    }

    public function codes()
    {
        $path = $this->filePath('php', 'codes');

        if (is_file($path)) {
            return require($path);
        }

        $data = $this->recursion($this->district());

        Write::phpFile($path, $data);

        return $data;
    }

    protected function recursion($data, &$codes = [])
    {
        foreach ($data as $item) {
            $districts = $item['districts'];
            if (!isset($codes[$item['adcode']])) {
                $codes[$item['adcode']] = $item['name'];
            }
            if ($districts) {
                $this->recursion($districts, $codes);
            }
        }

        ksort($codes);

        return $codes;
    }

    public function lists()
    {
        $path = $this->filePath('php', 'lists');

        if (is_file($path)) {
            return require($path);
        }

        $data = $this->listRecursion($this->district());

        Write::phpFile($path, $data);

        return $data;
    }

    protected function listRecursion($data, $parent_id = 0, &$codes = [])
    {
        $pinyin = new Pinyin();
        /**
         * 这里的路径要指向php目录下的，而不是postal目录下的
         *
         * 愿意是php目录下是行政区划代码与邮编
         *
         * postal目录下是名称与邮编，无法进行关联
         */
        $postals = require(PHP_PATH . '/Postals.php');
        foreach ($data as $item) {
            $districts = $item['districts'];
            $id = intval($item['adcode']);
            $letter = $pinyin->abbr($item['name']);
            if (!isset($codes[$id])) {
                $codes[$id] = [
                    'id' => $id,
                    'parent_id' => intval($parent_id),
                    'name' => $item['name'],
                    'center' => $item['center'],
                    'level' => $item['level'],
                    'postal' => $postals[$id] ?? '',
                    'tel' => $item['citycode'],
                    'letter' => ucfirst(substr($letter, 0, 1))
                ];
            }

            if ($districts) {
                $this->listRecursion($districts, $id, $codes);
            }
        }

        ksort($codes);

        return $codes;
    }

    public function level()
    {
        $path = $this->filePath('php', 'level');

        if (is_file($path)) {
            return require($path);
        }

        $data = $this->lists();

        $res = [];
        foreach ($data as $key => $item) {
            $id = $item['id'];
            $parent_id = $item['parent_id'];
            if (isset($data[$parent_id])) {
                $data[$parent_id]['children'][$id] = &$data[$key];
            } else {
                $res[$id] = &$data[$key];
            }
        }

        Write::phpFile($path, $res);

        return $data;
    }
}