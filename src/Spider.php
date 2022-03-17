<?php

require __DIR__ . '/../Make.php';

class Spider
{
    public function year2020()
    {
        $filename = __DIR__ . '/year/2020/origin.html';
        $codename = __DIR__ . '/year/2020/codes.php';
        if (!is_file($filename)) {
            $filepath = pathinfo($filename, PATHINFO_DIRNAME);
            if (!is_dir($filepath)) {
                mkdir($filepath, 0777);
            }
            $data = file_get_contents('http://www.mca.gov.cn/article/sj/xzqh/2020/20201201.html');
            file_put_contents($filename, $data);
        } else {
            $fs = fopen($filename, 'r');
            $data = fread($fs, filesize($filename));
        }

        preg_match_all('/mso-height-source:userset;height:14.25pt.*\s.*\s.*\s.*\s/', $data, $matches);

        $data = [];
        $string = '';
        if (isset($matches[0])) {
            foreach ($matches[0] as $item) {
                preg_match('/([0-9]{6})<\/td>.*\s.*>(.*)<\/td>/', $item, $mat);
                $region_code = $mat[1] ?? '';
                $region_name = $mat[2] ?? '';

                if ($region_code && $region_name) {
                    $data[$region_code] = $region_name;
                    $string .= "$region_code=>'$region_name',";
                    $string .= "\n";
                }
            }
            (new Make())->write($codename, $string);
        }

        return $data;
    }
}