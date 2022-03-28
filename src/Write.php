<?php

namespace Magein\ChinaDistrict;

class Write
{
    public static function phpFile($path, $data)
    {
        if (is_string($data)) {
            $content = $data;
        } elseif (is_array($data)) {
            $content = var_export($data, true);
            $content = preg_replace(['/=>.*\s.*array.*\(/', '/\),/'], ['=> [', '],'], $content);
            $content = trim($content);
            $content = trim($content, 'array (');
            $content = trim($content, ')');
        }

        if (empty($content)) {
            return false;
        }



        $data = "<?php";
        $data .= "\n";
        $data .= 'return [';
        $data .= $content;
        $data .= "];";

        file_put_contents($path, $data);

        return true;
    }

    public static function jsFile($path, $data)
    {

        if (empty($data)) {
            return false;
        }

        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        $data = 'export default ' . $data;

        file_put_contents($path, $data);

        return true;
    }

    public static function jsonFile($path, $data)
    {

        if (empty($data)) {
            return false;
        }

        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        file_put_contents($path, $data);

        return true;
    }
}