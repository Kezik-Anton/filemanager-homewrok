<?php

function removeDir($path)
{

    $dd = opendir($path);

    while (($item = readdir($dd)) !== false) {

        if ($item == '.' || $item == '..') {
            continue;
        }

        $item = $path . '\\' . $item;

        if (is_dir($item)) {
            removeDir($item);
        } else {
            unlink($item);
        }
    }
    rmdir($path);
}

function getFilesize($file)
{

    if (!file_exists($file)) return "no rights";

    $filesize = filesize($file);

    if ($filesize > 1024) {
        $filesize = ($filesize / 1024);

        if ($filesize > 1024) {
            $filesize = ($filesize / 1024);

            if ($filesize > 1024) {
                $filesize = round($filesize / 1024, 1);
                return $filesize . " ГБ";
            } else {
                $filesize = round($filesize, 1);
                return $filesize . " MБ";
            }
        } else {
            $filesize = round($filesize, 1);
            return $filesize . " Кб";
        }
    } else {
        $filesize = round($filesize, 1);
        return $filesize . " байт";
    }
}

function getFiledate($file)
{

    if (!file_exists($file)) return "no rights";

    return date('Y-m-d H:i', strtotime("+2 hours", filemtime($file)));
}

function checkExten($file)
{

    $fileExt = explode('.', $file);
    $ext = $fileExt[1] ?? false;

    $arrExten = explode('|', $GLOBALS['formats']);

    foreach ($arrExten as $exten) {
        if ($ext == $exten) return true;
    }
}

function translit($s)
{

    $s = preg_replace("/\s+/", ' ', $s);
    $s = trim($s);

    $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => '', 'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'e', 'Ж' => 'j', 'З' => 'z', 'И' => 'i', 'Й' => 'y', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sch', 'Ы' => 'y', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya', 'Ъ' => '', 'Ь' => ''));

    $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s);
    $s = str_replace(" ", "-", $s);

    return $s;
}

function reloadPage($dir) {
    header("location: /admin/?dir=$dir");
}
