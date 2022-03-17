<?php

class Make
{
    public function write($path, $content)
    {
        $data = "<?php";
        $data .= "\n";
        $data .= 'return [';
        $data .= "\n";
        $data .= $content;
        $data .= "  ];";
        file_put_contents($path, $data);
    }

    public function writeStatic($path, $content)
    {
        $path = __DIR__ . '/src/static/' . $path;
        $this->write($path, $content);
    }

    public function writeRegion($codes, $standard = [])
    {
        if (!is_array($standard)) {
            $standard = [];
        }

        $data = "";
        foreach ($codes as $item) {
            $data .= "  {$item['code']}=>[";
            $data .= "\n";
            unset($item['standard']);
            $is_standard = $standard[$item['code']] ?? '';
            unset($item['is_standard']);
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
        $this->writeStatic('Region.php', $data);
    }

    public function writeRegionCode($codes)
    {
        $data = '';
        foreach ($codes as $key => $item) {
            $data .= "  $key=>'" . $item['name'] . "',";
            $data .= "\n";
        }
        $this->writeStatic('RegionCode.php', $data);
    }

    public function writePostalCode($codes)
    {
        $data = '';
        foreach ($codes as $item) {
            $data .= "  '{$item['code']}'=>'" . $item['postal_code'] . "',";
            $data .= "\n";
        }
        $this->writeStatic('PostalCode.php', $data);
    }

    public function writeTelCode($codes)
    {
        $data = '';
        foreach ($codes as $item) {
            $data .= "  '{$item['code']}'=>'" . $item['tel_code'] . "',";
            $data .= "\n";
        }
        $this->writeStatic('TelCode.php', $data);
    }

    public function writeJson($data)
    {
        $this->write(__DIR__ . '/js/region_code.json', json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}