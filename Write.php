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
            $code = $item['id'];
            if (empty($code)) {
                continue;
            }

            $name = $item['name'];
            if (preg_match('/.*市$/', $name)) {
                $parent = $codes[$item['parent_id']];
                $parent_id = $parent['parent_id'];
                if ($parent_id == 0 && !$this->zxs($name)) {
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
                if (in_array($v, ['id', 'parent_id', 'postal', 'type'])) {
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
            $data .= "  '{$item['id']}'=>'" . $item['postal'] . "',";
            $data .= "\n";
        }
        $this->static('PostalCode.php', $data);
    }

    public function telCode($codes)
    {
        $data = '';
        foreach ($codes as $item) {
            $data .= "  '{$item['id']}'=>'" . $item['tel'] . "',";
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

        $records = [];
        foreach ($data as $item) {
            if ($item['parent_id'] == 0) {
                $records[$item['id']] = [
                    'id' => $item['id'],
                    'parent_id' => $item['parent_id'],
                    'name' => $item['name'],
                    'children' => []
                ];
            }
        }

        $codes = require('./src/static/RegionCode.php');

        foreach ($data as $item) {
            $parent_id = $item['parent_id'];
            if ($parent_id == 0) {
                continue;
            }
            if (isset($records[$parent_id])) {
                $code = $item['id'];
                $children = [];
                foreach ($data as $val) {
                    if ($val['parent_id'] == $code) {
                        if (isset($codes[$val['id']])) {
                            $children[] = [
                                'id' => $val['id'],
                                'parent_id' => $val['parent_id'],
                                'name' => $val['name']
                            ];
                        }
                    }
                }

                $records[$parent_id]['children'][$code] = [
                    'id' => $code,
                    'parent_id' => $item['parent_id'],
                    'name' => $item['name'],
                    'children' => $children
                ];
            }
        }

        $this->json('region_children_code.json', json_encode($records, JSON_UNESCAPED_UNICODE));

        return $records;
    }
}