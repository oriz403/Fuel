<?php

/**
 * ログ出力ユーティリティクラス。
 *
 * @package Fuel
 * @author Ory43
 * @since 2015/06/07
 *
 */
class Util_Log
{

    const CONFIG = '%s%s: %s';

    const IS_UNDEFINDE = '%s is undefined.';

    const IS_NOT_EXISTS = '%s is not exists.';

    const TITLE = '**** %s ****';

    public static function show($log)
    {
        echo $log . PHP_EOL;
    }

    public static function showConfig($key, $value)
    {
        // $key: $value
        Util_Log::show(sprintf(Util_Log::CONFIG, $key, Util_String::indent($key), $value));
    }

    public static function showUndefinedError($key)
    {
        // $key is undefined.
        Util_Log::show(sprintf(Util_Log::IS_UNDEFINDE, $key));
    }

    public static function showNotExistsError($key)
    {
        // $key is not exists.
        Util_Log::show(sprintf(Util_Log::IS_NOT_EXISTS, $key));
    }

    public static function showTitle($title)
    {
        // ** $title **
        echo sprintf(PHP_EOL . Util_Log::TITLE . PHP_EOL, $title);
    }
}
