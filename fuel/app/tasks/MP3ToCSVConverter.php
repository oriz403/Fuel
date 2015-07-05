<?php
namespace Fuel\Tasks;

/**
 * CSV形式のMP3リスト出力バッチ。
 * 【使用方法】php oil r MP3ToCSVConverter
 * 第1引数: {CSV形式に変換する対象のディレクトリ}
 *
 * @package Fuel
 * @author Ory43
 * @since 2015/05/27
 *
 */
class MP3ToCSVConverter
{

    /**
     * タイトル
     *
     * @var string
     */
    const TITLE = 'mp3 to csv converter';

    /**
     * (定数)MP3が存在するミュージックディレクトリ
     *
     * @var string
     */
    const MP3_DIR = 'H:\\03_music\\01_public\\';

    /**
     * (定数)出力するCSVのファイルパス
     *
     * @var string
     */
    const CSV_PATH = 'H:\\03_music\\';

    /**
     * 親ディレクトリ
     *
     * @var string
     */
    protected $parentDir = '';

    /**
     * MP3ファイル数
     *
     * @var int
     */
    protected $mp3Count = 0;

    /**
     * ディレクトリ階層レベル
     *
     * @var int
     */
    protected $level = 0;

    /**
     * MP3名格納リスト
     *
     * @var array
     */
    protected $mp3List = array();

    /**
     * CSV形式のMP3リスト出力バッチの実行。
     *
     * @param string $DirName
     *            MP3_DIR配下に存在するディレクトリ
     */
    public function run($dir = null)
    {
        if ($this->checkConst() === 0) {
            // 当該バッチの定数が正しいファイルパスでなければ処理終了
            return 0;
        }

        if (isset($dir) === true) {
            // 引数でディレクトリが指定されていた場合はそのディレクトリまでを親ディレクトリとしてセット
            $this->setParentDir(MP3ToCSVConverter::MP3_DIR . $dir);

            // セットした親ディレクトリが正しいファイルパスでなければエラー文言を出力し処理終了
            if (file_exists($this->parentDir) === false) {
                \Util_Log::showNotExistsError('Parent Directory');
                \Util_Log::showConfig('filePath', $this->parentDir);
                return 0;
            }
        } else {
            // 引数でディレクトリが指定されていなければ(定数)MP3_DIRを親ディレクトリとしてセット
            $this->setParentDir(MP3ToCSVConverter::MP3_DIR);
        }

        // ファイル/ディレクトリ一覧を表示
        $this->showFileList($this->parentDir);

        // CSV形式に変換
        //$this->convertCSV($this->mp3List);

        // MP3数を表示
        \Util_Log::showConfig('mp3', $this->mp3Count);
    }

    /**
     * 定数が正しくセットされており、かつ正しいファイルパスかどうかをチェックする。
     *
     * @return void
     */
    protected function checkConst()
    {
        $dir = MP3ToCSVConverter::MP3_DIR;

        // (定数)MP3_DIRが未設定であればエラー文言を出力し処理終了
        if (empty($dir) === true) {
            \Util_Log::showUndefinedError('[const] MP3ToCSVConverter::MP3_DIR');
            return 0;
        }

        // (定数)MP3_DIRに設定されているファイルパスが正しくなければエラー文言を出力し処理終了
        if (file_exists($dir) === false) {
            \Util_Log::showNotExistsError('[const] MP3ToCSVConverter::MP3_DIR');
            \Util_Log::showConfig('filePath', $dir);
            return 0;
        }
    }

    /**
     * 親ディレクトリ $parentDir をセットする。
     *
     * @param unknown $parentDir
     * @return \Fuel\Tasks\MP3ToCSVConverter
     */
    protected function setParentDir($parentDir)
    {
        $this->parentDir = $parentDir;

        return $this;
    }

    /**
     * 指定されたディレクトリ $dir 配下のファイル/ディレクトリ一覧を表示する。
     *
     * @param string $dir
     *            ディレクトリ
     * @return void
     */
    protected function showFileList($dir)
    {
        // ディレクトリ配下のファイル/ディレクトリ一覧を取得
        $files = scandir($dir);

        // '.'もしくは'..'が含まれない配列にフィルタリング
        $files = array_filter($files, function ($file)
        {
            // ディレクトリ名が'.'もしくは'..'であればfalseを返却
            return ! in_array($file, array(
                '.',
                '..'
            ));
        });

        foreach ($files as $file) {
            // \\があればトリムしたあと\\を付加し、ファイルパスを生成
            $path = rtrim($dir, '\\') . '\\' . $file;

            if (is_file($path) === true && preg_match('/.mp3$/', $path) === 1) {
                // mp3ファイルであれば曲名を出力
                echo sprintf('%s %s' . PHP_EOL, $this->getDispLevel($this->level), $file);

                // MP3リストに格納
                $this->mp3List[] = $path;

                // MP3数を加算
                $this->mp3Count ++;
            }

            if (is_dir($path) === true) {
                // ディレクトリであればディレクトリ名を出力
                echo sprintf('%s %s' . PHP_EOL, $this->getDispLevel($this->level ++), $file);

                // ディレクトリであればそのディレクトリ配下のファイル/ディレクトリ一覧を取得
                $this->showFileList($path);

                // 階層レベルダウン
                $this->level --;
            }
        }
    }

    /**
     * ディレクトリ階層レベルより表示用階層レベル文字列を取得する。
     *
     * @param int $level
     *            ディレクトリ階層レベル
     * @return string 表示用階層レベル文字列
     */
    protected function getDispLevel($level)
    {
        $string = '';

        for ($i = 0; $i < $level; $i ++) {
            $string .= '-';
        }

        return $string;
    }

    /**
     *
     * @param unknown $mp3List
     */
    protected function convertCSV($mp3List)
    {
        // CSV出力先
        $csvPath = MP3ToCSVConverter::CSV_PATH;
        $csv = '';

        // MP3レコード分ループ
        foreach ($mp3List as $value) {
            // カンマと改行の対応
            $value = str_replace(',', '","', $value);
            $value = str_replace("/n", chr(10), $value);

            // CSV生成
            $csv .= $value . ',';
            $csv .= "/n";
        }

        // CSV出力
        // TODO fuel寄せ
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$csvPath");

        // Excelで開けるように言語対応
        echo mb_convert_encoding($csv, "SJIS", "UTF-8");
    }
}