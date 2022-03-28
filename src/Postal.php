<?php

namespace Magein\ChinaDistrict;

class Postal
{
    public function make()
    {
        $this->detail();

        $this->match();
    }

    public function move()
    {
        $path = POSTAL_PATH . '/Postals.php';
        if (!is_file($path)) {
            return false;
        }

        $data = require($path);
        $district_code = require(PHP_PATH . '/DistrictCode.php');

        $postals = [];
        foreach ($data as $item) {
            $name = trim($item['name']);
            $postal = trim($item['postal']);
            $code = array_search($name, $district_code);
            if ($code) {
                $postals[$code] = $postal;
            }
        }
        Write::phpFile(PHP_PATH . '/Postals.php', $postals);

        $path = POSTAL_PATH . '/Tels.php';
        if (!is_file($path)) {
            return false;
        }

        $data = require($path);

        $tels = [];
        foreach ($data as $item) {
            $name = trim($item['name']);
            $tel = trim($item['tel']);
            $code = array_search($name, $district_code);
            if ($code) {
                $tels[$code] = $tel;
            }
        }
        Write::phpFile(PHP_PATH . '/Tels.php', $tels);

        return true;
    }

    protected function province()
    {
        $path = POSTAL_PATH . '/data/province.php';
        if (is_file($path)) {
            return require($path);
        }

        $province = file_get_contents('https://www.ip138.com/post/');

        preg_match('/id="quanguo".*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s.*\s/', $province, $matches);

        $hrefs = $matches[0];

        preg_match_all('/href="\/([\w]+)/', $hrefs, $matches);

        $province = $matches[1] ?? [];

        Write::phpFile($path, $province);

        return $province;
    }

    protected function detail()
    {
        $provinces = $this->province();

        foreach ($provinces as $item) {
            $path = POSTAL_PATH . "/detail/$item.html";
            if (is_file($path)) {
                continue;
            }
            file_put_contents($path, file_get_contents("https://www.ip138.com/$item/"));
        }

        return true;
    }

    protected function match()
    {
        $path = POSTAL_PATH . '/Postals.php';
        if (is_file($path)) {
            return true;
        }

        $lists = glob(POSTAL_PATH . "/detail/*.html");

        $postals = [];
        $tels = [];
        if ($lists) {
            foreach ($lists as $item) {
                $content = file_get_contents($item);
                preg_match('/<tr bgcolor="#ffffff">.*<\/tr>/', $content, $matches);
                $data = $matches[0];
                $data = preg_replace('/<tr bgcolor="#ffffff"><td>/', '', $data);
                $data = preg_replace('/<a href="\/?[a-zA-Z]+\/?">/', '', $data);
                $data = preg_replace('/<b>|<\/b>/', '', $data);
                $data = preg_replace('/<\/a>|<\/?td>|<\/?tr>/', ',', $data);
                $data = preg_replace('/&nbsp;/', '', $data);
                $data = preg_replace('/<td colspan="[0-9]?">/', ',', $data);
                $data = preg_replace('/<a href=/', ',', $data);
                $data = explode(',', $data);
                $data = array_filter($data);

                $data = array_chunk($data, 3);

                foreach ($data as $val) {
                    $name = trim($val[0] ?? '');
                    $postal = $val[1] ?? '';
                    $tel = $val[2] ?? '';

                    if ($postal) {
                        preg_match('/[\w]+/', $postal, $matches);
                        $postal = $matches[0] ?? '';
                    }
                    if ($tel) {
                        preg_match('/[\w]+/', $tel, $matches);
                        $tel = $matches[0] ?? '';
                    }

                    $p_len = strlen($postal);
                    $t_len = strlen($tel);

                    if ($p_len <= 4 && $t_len === 6) {
                        list($postal, $tel) = [$tel, $postal];
                    }

                    if ($postal && strlen($postal) == 6) {
                        $postals[] = [
                            'name' => $name,
                            'postal' => $postal
                        ];
                    }

                    if ($tel && strlen($tel) <= 4) {
                        $tels[] = [
                            'name' => $name,
                            'tel' => $tel
                        ];
                    }
                }
            }
        }

        Write::phpFile(POSTAL_PATH . '/Postals.php', $postals);
        Write::phpFile(POSTAL_PATH . '/Tels.php', $tels);
    }

}