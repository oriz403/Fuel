<?php
namespace Fuel\Tasks;

/**
 * パスワード生成バッチ。
 * 【使用方法】php oil r PasswordGenerator
 * 第1引数: {パスワードの文字数}
 * 第2引数: {重複を許すか '1' or '0'}
 * 第3引数:
 *
 * @package Fuel
 * @author Ory43
 * @since 2015/05/23
 *
 */
class PasswordGenerator extends \Tasks_Base
{

    /**
     * タイトル
     *
     * @var string
     */
    protected $title = 'password generator';

    /**
     * (定数)生成するパスワードの総数
     *
     * @var int
     */
    const MAX_VALUE = 30;

    /**
     * (定数)一行に出力するパスワード数
     *
     * @var int
     */
    const WIDTH = 5;

    /**
     * パスワードの長さ
     *
     * @var int
     */
    protected $length = 0;

    /**
     * 重複を許すかどうかを判別するフラグ
     * true : 許す
     *
     * @var int
     */
    protected $isOverlapFlg = true;

    /**
     * パスワードに出現しやすくなるお気に入り文字の配列
     *
     * @var string
     */
    protected $favorites = array();

    /**
     * 英数字配列([a-z][A-Z][0-9])
     *
     * @var array
     */
    protected $alphanumeric = array();

    /**
     * パスワードの配列
     *
     * @var array
     */
    protected $password = array();

    /**
     * 重複を許さないための無視リスト
     *
     * @var array
     */
    protected $ignore = array();

    /**
     * パスワード生成バッチの実行。
     *
     * @param string $length
     *            パスワードの長さ(デフォルトは8文字)
     * @param string $isOverlapFlg
     *            重複を許すかどうか
     * @param string $favorites
     *            パスワードに出現しやすくなるお気に入り文字列
     *
     * @return void
     */
    public function run($length = 8, $isOverlapFlg = true, $favorites = null)
    {
        // パスワード生成に用いる情報をセット
        $this->setLength((int) $length);
        $this->setIsOverlapFlg((boolean) $isOverlapFlg);
        $this->setFavorites($favorites);

        // パスワードで用いる英数字配列をセット
        $this->setAlphanumeric($this->favorites);

        // パスワード生成に用いる情報を出力
        $this->showConfig($length, $isOverlapFlg, $favorites);

        // パスワードを個数分取得
        $this->password = $this->getPasswordArr();

        // 整形されたパスワード一覧を出力
        $this->showPasswords();
    }

    /**
     * パスワードの長さ $length をセットする。
     *
     * @param int $length
     *            パスワードの長さ
     *
     * @return \Fuel\Tasks\PasswordGenerator
     */
    protected function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * 重複を許すかどうか $isOverlapFlg をセットする。
     *
     * @param boolean $isOverlapFlg
     *            重複を許すかどうか
     *
     * @return \Fuel\Tasks\PasswordGenerator
     */
    protected function setIsOverlapFlg($isOverlapFlg)
    {
        $this->isOverlapFlg = $isOverlapFlg;

        return $this;
    }

    /**
     * パスワードに出現しやすくなるお気に入り文字列 $favorites を1文字ずつ分解し、配列にセットする。
     *
     * @param string $favorites
     *            パスワードに出現しやすくなるお気に入り文字列
     *
     * @return \Fuel\Tasks\PasswordGenerator
     */
    protected function setFavorites($favorites)
    {
        $array = array();

        if (isset($favorites) === true) {
            foreach (str_split($favorites) as $val) {
                array_push($array, $val);
            }
        }

        $this->favorites = $array;

        return $this;
    }

    /**
     * 英数字配列をセットする。
     *
     * @param array $favorites
     *            パスワードに出現しやすくなるお気に入り文字の配列
     *
     * @return \Fuel\Tasks\PasswordGenerator
     */
    protected function setAlphanumeric($favorites)
    {
        $this->alphanumeric = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'), $favorites);

        return $this;
    }

    /**
     * バッチの設定を出力する。
     *
     * @param string $length
     *            パスワードの長さ
     * @param string $isOverlapFlg
     *            重複を許すかどうか
     * @param string $favorites
     *            パスワードに出現しやすくなるお気に入り文字列
     *
     * @return void
     */
    protected function showConfig($length, $isOverlapFlg, $favorites)
    {
        \Util_Log::showConfig('pass length', $length);
        \Util_Log::showConfig('is overlap', ((boolean) $isOverlapFlg === true) ? 'yes' : 'no');
        \Util_Log::showConfig('favorites', $favorites);
    }

    /**
     * パスワードの配列を取得する。
     *
     * @return array パスワードの配列
     */
    protected function getPasswordArr()
    {
        $array = array();
        $i = 0;

        while ($i < PasswordGenerator::MAX_VALUE) {
            $this->ignore = array();
            $array[$i ++] = $this->getPassword();
        }

        return $array;
    }

    /**
     * 長さ分のパスワードを取得する。
     *
     * @return string 生成したパスワード
     */
    protected function getPassword()
    {
        $password = '';
        $i = 0;

        // セットした長さ分のパスワードを生成
        while ($i ++ < $this->length) {
            // 重複を許す場合と許さない場合でパスワード生成方法を変更
            $password .= ($this->isOverlapFlg === true) ? $this->getRandStr() : $this->getNonOverlapStr();
        }

        return $password;
    }

    /**
     * ランダムな英数字1文字を取得する。
     *
     * @return string ランダムな英数字1文字
     */
    protected function getRandStr()
    {
        // 英数字配列内のランダムな英数字1文字を返却
        return $this->alphanumeric[rand(0, count($this->alphanumeric) - 1)];
    }

    /**
     * 重複しないランダムな英数字1文字を取得する。
     *
     * @return string 重複しないランダムな英数字1文字
     */
    protected function getNonOverlapStr()
    {
        do {
            // 英数字配列内のランダムな英数字1文字を取得
            $rand = rand(0, count($this->alphanumeric) - 1);
            // 重複しない英数字になるまでループ
        } while (in_array($rand, $this->ignore) === true);

        // 重複していない英数字の番号を無視リストに追加
        array_push($this->ignore, $rand);

        foreach ($this->alphanumeric as $key => $val) {
            // 同じ文字は全て無理リストに追加
            if (strcmp($this->alphanumeric[$rand], $val) === 0) {
                array_push($this->ignore, $key);
            }
        }

        // 重複していない英数字1文字を返却
        return $this->alphanumeric[$rand];
    }

    /**
     * 生成されたパスワードを見やすいように整列して出力する。
     *
     * @return void
     */
    protected function showPasswords()
    {
        // タイトルを出力
        \Util_Log::showTitle('generate password');

        // 結果を整列して出力
        \Util_Log::show(\Util_String::align($this->password, PasswordGenerator::WIDTH));
    }
}