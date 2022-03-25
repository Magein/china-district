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

    public function district($codes, $standard = [])
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
        $this->static('District.php', $data);
    }

    public function districtCode($codes)
    {
        $data = '';
        foreach ($codes as $key => $item) {

            if ($item['type'] != 1 && !!$this->zxs($item['name'])) {
                continue;
            }

            $data .= "  $key=>'" . $item['name'] . "',";
            $data .= "\n";
        }
        $this->static('DistrictCode.php', $data);
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

    public function jsonDistrictCode()
    {
        $data = require(__DIR__ . '/src/static/DistrictCode.php');
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->json('district_code.json', $data);

        $data = preg_replace('/"([0-9]{6})"/', '$1', $data);
        $this->json('district_code.js', 'const districtCode=' . $data);
    }

    public function districtChildren($data)
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

        $codes = require('./src/static/DistrictCode.php');

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


        $framework = [];
        foreach ($records as $item) {

            $childrens = array_values($item['children']);

            foreach ($childrens as $key => $children) {

                $chs = array_values($children['children']);
                foreach ($chs as $k => $ch) {
                    $chs[$k] = [
                        'value' => $ch['id'],
                        'label' => $ch['name'],
                    ];
                }

                $childrens[$key] = [
                    'value' => $children['id'],
                    'label' => $children['name'],
                    'children' => $chs
                ];
            }

            $framework[] = [
                'value' => $item['id'],
                'label' => $item['name'],
                'children' => $childrens
            ];
        }
        $framework = json_encode($framework, JSON_UNESCAPED_UNICODE);
        $this->json('framework.js', 'export default ' . $framework);


        $data = json_encode($records, JSON_UNESCAPED_UNICODE);
        $this->json('district_children_code.json', $data);

        $data = preg_replace('/"([0-9]{6})"/', '$1', $data);
        $this->json('district_children_code.js', 'const districtChildren=' . $data);

        return $records;
    }
}