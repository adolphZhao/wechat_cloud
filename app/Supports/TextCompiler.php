<?php
namespace App\Supports;


class TextCompiler
{
    const PREFIX = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAkCAYAAABIdFAMAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbW';

    public static function scriptToBase64($filename, $packer = true)
    {
        $script = file_get_contents($filename);

        if ($packer) {

            $packer = new JavaScriptPacker($script, 'Normal', true, false);

            $packed = $packer->pack();

        } else {

            $packed = $script;

        }

        $base64data = self::PREFIX . base64_encode($packed);

        return $base64data;
    }

    public static function fileToBase64($filename)
    {
        $script = file_get_contents($filename);

        $base64data = base64_encode($script);

        return $base64data;
    }

    public static function dataToBase64($data)
    {
        $json = json_encode($data);

        $base64data = self::PREFIX . base64_encode($json);

        return $base64data;
    }
}