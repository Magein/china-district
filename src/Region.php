<?php

namespace Magin\region;

class Region
{

    private $filename = '';

    public function getRegionCode($name = '')
    {
        $this->filename = 'RegionCode';

        return $this->getCode($name);
    }

    public function getTelCode($name)
    {

        $this->filename = 'TelCode';

        return $this->getCode($name);
    }

    public function getPostalCode($name)
    {
        $this->filename = 'PostalCode';

        return $this->getCode($name);
    }

    private function getCode($name)
    {
        $result = $this->loadFile($this->filename);

        $code = '';

        if ($result && isset($result[$name])) {
            $code = $result[$name];
        }

        return $code;
    }

    private function loadFile($name)
    {
        $filename = '../code/' . $name . '.php';

        if (is_file($filename)) {
            return require_once "$filename";
        }

        return [];
    }
}