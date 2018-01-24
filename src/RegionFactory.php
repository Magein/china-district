<?php

namespace magein;

class RegionFactory
{
    /**
     * 获取地区编号
     * @param string|null $name
     * @return array|mixed|null
     */
    public function getAreaCode(string $name = null)
    {
        $resource = $this->getCode('AreaCode');

        if ($name) {

            if (isset($resource[$name])) {
                return $resource[$name];
            }

            return null;
        }

        return $resource;
    }

    /**
     * 获取邮编
     * @param string|null $name
     * @return array|mixed|null
     */
    public function getPostalCode(string $name = null)
    {
        $resource = $this->getCode('PostalCode');

        if ($name) {

            if (isset($resource[$name])) {
                return $resource[$name];
            }

            return null;
        }

        return $resource;
    }

    /**
     * @param $name
     * @return array|mixed
     */
    private function getCode($name)
    {
        $path = './src/code/' . $name . '.php';

        if (is_file($path)) {
            return require_once $path;
        }

        return [];
    }
}