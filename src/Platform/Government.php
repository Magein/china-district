<?php

namespace Magein\ChinaDistrict\Platform;

use Magein\ChinaDistrict\Platform;
use Magein\ChinaDistrict\Write;

class Government extends Platform
{

    public function name()
    {
        return 'government';
    }

    protected function yearUrl()
    {
        $data = [
            2022 => 'http://www.mca.gov.cn/article/sj/xzqh/2020/20201201.html',
            2021 => 'http://www.mca.gov.cn/article/sj/xzqh/2020/20201201.html',
            2020 => 'http://www.mca.gov.cn/article/sj/xzqh/2020/20201201.html',
        ];

        return $data[date('Y')];
    }

    protected function path($name)
    {
        $year = date('Y');
        return __DIR__ . "/Government/$year/$name";
    }

    public function district()
    {
        $data_path = $this->path('data.php');
        if (is_file($data_path)) {
            return require($data_path);
        }
        $filename = $this->path('origin.html');
        if (!is_file($filename)) {
            $filepath = pathinfo($filename, PATHINFO_DIRNAME);
            if (!is_dir($filepath)) {
                mkdir($filepath, 0777);
            }
            $data = file_get_contents($this->yearUrl());
            if ($data) {
                file_put_contents($filename, $data);
            }
        } else {
            $fs = fopen($filename, 'r');
            $data = fread($fs, filesize($filename));
        }

        preg_match_all('/mso-height-source:userset;height:14.25pt.*\s.*\s.*\s.*\s/', $data, $matches);

        $data = [];
        if (isset($matches[0])) {
            foreach ($matches[0] as $item) {
                preg_match('/([0-9]{6})<\/td>.*\s.*>(.*)<\/td>/', $item, $mat);
                $code = $mat[1] ?? '';
                $name = $mat[2] ?? '';
                if ($code && $name) {
                    $int_code = trim($code, 0);
                    if (strlen($int_code) <= 4) {
                        $name = preg_replace('/市/', '', $name);
                    }
                    $data[$code] = $name;
                }
            }
            $data[110100] = '北京市';
            $data[310100] = '上海市';
            $data[500100] = '重庆市';
            $data[120100] = '天津市';
        }

        Write::phpFile($data_path, $data);

        return $data;
    }

    public function codes()
    {
        return require($this->path('data.php'));
    }

    public function lists()
    {

    }

    public function level()
    {

    }
}