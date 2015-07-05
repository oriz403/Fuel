<?php

/**
 * ストリング型を扱うユーティリティクラス。
 *
 * @package Fuel
 * @author Ory43
 * @since 2015/06/07
 *
 */
class Util_String
{

    const TAB = 4;

    protected static $strTab = '';

    public static function _init()
    {
        Util_String::setStrTab();
    }

    protected static function setStrTab()
    {
        $space = '';
        $i = 0;

        while ($i ++ < Util_String::TAB) {
            $space .= ' ';
        }

        Util_String::$strTab = $space;
    }

    /**
     * 指定された文字列の前後に埋めるためのスペースを作成する。
     *
     * @param string $str
     *            文字列
     *
     * @return string 処理結果の文字列
     */
    public static function indent($str)
    {
        $space = '';
        $length = mb_strlen($str);

        while ($length ++ % Util_String::TAB !== 0) {
            $space .= ' ';
        }

        return $space;
    }

    /**
     * 指定された配列を一行に $width 分表示したブロックを作成する。
     *
     * @param array $array
     *            配列
     * @param int $width
     *            一行に表示する要素数
     *
     * @return string 処理結果の文字列
     */
    public static function align($array, $width)
    {
        $result = '';
        $i = 0;

        do {
            // 配列の要素を出力
            $result .= $array[$i ++];

            // 行の末尾の要素であれば改行、そうでなければタブを出力
            $result .= (($i % $width) === 0) ? PHP_EOL : Util_String::$strTab;
            // 配列の最後の要素になるまでループ
        } while ($i < count($array));

        return $result;
    }
}
