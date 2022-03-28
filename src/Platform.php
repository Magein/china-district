<?php

namespace Magein\ChinaDistrict;

abstract class Platform
{
    abstract public function name();

    abstract public function district();

    abstract public function codes();

    abstract public function lists();

    abstract public function level();

    public function makeDistrict()
    {
        $code = $this->lists();
        $path = PHP_PATH . '/District.php';
        if ($code) {
            Write::phpFile($path, $code);
        }

        if (is_file($path)) {
            return true;
        }

        return false;
    }

    public function makeDistrictCode()
    {
        $code = $this->codes();
        $path = PHP_PATH . '/DistrictCode.php';
        if ($code) {
            Write::phpFile($path, $code);
        }

        if (is_file($path)) {
            return true;
        }

        return false;
    }

    public function makeDistrictLevel()
    {
        $code = $this->level();
        $path = PHP_PATH . '/DistrictLevel.php';
        if ($code) {
            Write::phpFile($path, $code);
        }

        if (is_file($path)) {
            return true;
        }

        return false;
    }

    public function makeFramework()
    {
        $path = PHP_PATH . '/District.php';

        if (!is_file($path)) {
            return false;
        }

        $data = require($path);

        $res = [];
        $codes = [];
        foreach ($data as $datum) {
            $res[$datum['id']] = [
                'value' => $datum['id'],
                'label' => $datum['name'],
                'parent_id' => $datum['parent_id'],
            ];
            $codes[$datum['id']] = $datum['name'];
        }

        $level = [];
        foreach ($res as $key => $item) {
            $parent_id = $item['parent_id'];
            if (isset($res[$parent_id])) {
                $res[$parent_id]['children'][] = &$res[$key];
            } else {
                $level[] = &$res[$key];
            }
        }

        $path = JS_PATH . '/district_level.js';
        Write::jsFile($path, $level);

        $path = JS_PATH . '/district.js';
        $codes = preg_replace('/"([0-9]{6})"/', '$1', json_encode($codes, JSON_UNESCAPED_UNICODE));
        Write::jsFile($path, $codes);

        return true;
    }

    public function makeJson()
    {

        $data = [
            'District' => 'district',
            'DistrictCode' => 'district_code',
            'DistrictLevel' => 'district_level',
        ];

        foreach ($data as $key => $item) {
            $path = PHP_PATH . "/$key.php";
            if (!is_file($path)) {
                continue;
            }
            $data = require($path);
            $path = JSON_PATH . "/$item.json";
            Write::jsonFile($path, $data);
        }

        return true;
    }
}