<?php
if (!defined('DXMA_VERSION')) die();

function createIfNotExist(string $path) {
    if (!is_dir($path))
        mkdir($path, 0777, TRUE);
}

function deltree(string $path) {
    if (is_dir($path)) {
        $objects = scandir($dir);
        foreach ($objects as $obj) {
            if ($obj != "." && $obj != "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $obj) && !is_link($dir . DIRECTORY_SEPARATOR . $obj))
                    deltree($dir . DIRECTORY_SEPARATOR . $obj);
                else
                    unlink($dir . DIRECTORY_SEPARATOR . $obj); 
            }
        }
        rmdir($path);
    }
}

function renameOrMerge(string $src, string $dst) {
    if (!is_dir($src))
        rename($src, $dst);
    else {
        $objects = scandir($src);
        foreach ($objects as $obj) {
            if ($src != "." && $src != "..") {
                rename($src . DIRECTORY_SEPARATOR . $obj, $dst . DIRECTORY_SEPARATOR . $obj); 
            }
        }
        deltree($src);
    }
}

function getMissionFilePath(string $uid, string $name, string $fn = NULL) {
    $path = FILEPATH . DIRECTORY_SEPARATOR . "f" . DIRECTORY_SEPARATOR . strval($uid) . DIRECTORY_SEPARATOR . $name;
    createIfNotExist($path);
    if (!is_null($fn)) {
        $path .= DIRECTORY_SEPARATOR . $fn;
    }
    return $path;
}

function getScreenshotFilePath(string $uid, string $name, string $fn = NULL) {
    $path = FILEPATH . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . strval($uid) . DIRECTORY_SEPARATOR . $name;
    createIfNotExist($path);
    if (!is_null($fn)) {
        $path .= DIRECTORY_SEPARATOR . $fn;
    }
    return $path;
}

function getMissionFileURL(string $uid, string $name, string $fn = NULL) {
    $path = FILEURL . "/f/" . strval($uid) . "/" . $name;
    if (!is_null($fn)) {
        $path .= DIRECTORY_SEPARATOR . $fn;
    }
    return $path;
}

function getScreenshotFileURL(string $uid, string $name, string $fn = NULL) {
    $path = FILEURL . "/img/" . strval($uid) . "/" . $name;
    if (!is_null($fn)) {
        $path .= DIRECTORY_SEPARATOR . $fn;
    }
    return $path;
}

function formatFileSize(int $size) {
    $base = floor(log($size) / log(1024));
    $suffix = array("B", "kB", "MB", "GB", "TB")[$base];
    return number_format($size / pow(1024, $base), 1) . " " . $suffix;
}
?>
