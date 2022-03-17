<?php

class Write
{
    private function zxs($name)
    {
        if (in_array($name, ['北京市', '北京', '上海市', '上海', '天津市', '天津', '重庆市', '重庆'])) {
            return true;
        } else {
            return false;
        }
    }

    protected function php($path, $content)
    {
        $data = "<?php";
        $data .= "\n";
        $data .= 'return [';
        $data .= "\n";
        $data .= $content;
        $data .= "  ];";
        file_put_contents($path, $data);
    }

    private function static($path, $content)
    {
        $path = __DIR__ . '/src/static/' . $path;
        $this->php($path, $content);
    }

    private function json($path, $content)
    {
        $path = __DIR__ . '/src/js/' . $path;
        file_put_contents($path, $content);
    }

    public function region($codes, $standard = [])
    {
        if (!is_array($standard)) {
            $standard = [];
        }

        $data = "";
        foreach ($codes as $item) {
            $code = $item['code'];
            if (empty($code)) {
                continue;
            }

            $name = $item['name'];
            if (preg_match('/.*市$/', $name)) {
                $parent = $codes[$item['parent_code']];
                $parent_code = $parent['parent_code'];
                if ($parent_code == 0 && !$this->zxs($name)) {
                    $name = mb_substr($name, 0, -1);
                }
            }
            $item['name'] = $name;

            $data .= "  $code=>[";
            $data .= "\n";
            $is_standard = $standard[$code] ?? '';
            // 1 标准的 0 不是
            $item['type'] = $is_standard ? 1 : 0;

            foreach ($item as $v => $val) {
                if (in_array($v, ['code', 'parent_code', 'postal_code', 'type'])) {
                    $data .= "      '$v'=>$val,";
                } else {
                    $data .= "      '$v'=>'$val',";
                }
                $data .= "\n";
            }
            $data .= "  ],";
            $data .= "\n";
        }
        $this->static('Region.php', $data);
    }

    public function regionCode($codes)
    {
        $data = '';
        foreach ($codes as $key => $item) {

            if ($item['type'] != 1 && !!$this->zxs($item['name'])) {
                continue;
            }

            $data .= "  $key=>'" . $item['name'] . "',";
            $data .= "\n";
        }
        $this->static('RegionCode.php', $data);
    }

    public function postalCode($codes)
    {
        $data = '';
        foreach ($codes as $item) {
            $data .= "  '{$item['code']}'=>'" . $item['postal_code'] . "',";
            $data .= "\n";
        }
        $this->static('PostalCode.php', $data);
    }

    public function telCode($codes)
    {
        $data = '';
        foreach ($codes as $item) {
            $data .= "  '{$item['code']}'=>'" . $item['tel_code'] . "',";
            $data .= "\n";
        }
        $this->static('TelCode.php', $data);
    }

    public function jsonRegionCode()
    {
        $data = require(__DIR__ . '/src/static/RegionCode.php');

        $this->json('region_code.json', json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function jsonRegionChild($data)
    {
        $this->json('region_code.json', json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function regionChildren($data)
    {
        $recursion = function ($data) {
            $relation_field = 'parent_code';
            $result = [];
            foreach ($data as $key => $item) {
                if ($item['type'] != 1 && !$this->zxs($item['name'])) {
                    continue;
                }
                if (isset($data[$item[$relation_field]])) {
                    $data[$item[$relation_field]]['children'][] = &$data[$key];
                } else {
                    $result[] = &$data[$key];
                }
            }
            return $result;
        };

        return $recursion($data);
    }
}