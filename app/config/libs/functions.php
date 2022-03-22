<?php
// add more functions
/**
 * 拡張子の取得
 * @param string $filename original file name
 * */
function getExtension($filename)
{
    return strtolower(substr(strrchr($filename, '.'), 1));
}
