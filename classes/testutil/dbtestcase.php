<?php

namespace TestUtil;

use Fuel\Core\DB;
use Profiler; /* 拡張した Profiler となる */

/**
 * DbTestCase
 *
 * @author kawamurayuto
 */
abstract class DbTestCase extends \Fuel\Core\TestCase
{

    /**
     * テストで使用するテーブル名の定義
     * 
     *  protected $tables = [
     *      'table_name' => [
     *          true, // オプション：行のコピーをするかどうか（デフォルト：false）
     *          10,   // オプション：コピーする行の件数（デフォルト:null）
     *      ]
     *  ];
     * 
     * @var array
     */
    protected $tables = [];
    private static $created_tables = [];
    private static $shutdown_handler;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (!self::$shutdown_handler) {
            self::$shutdown_handler = [$this, 'dropTables'];
            register_shutdown_function(self::$shutdown_handler);
        }

        Profiler::set_ignore_log_pattern('/^(TRUNCATE|CREATE|DROP)/');
    }

    public function dropTables()
    {
        foreach (self::$created_tables as $table => $params) {
            $sql = sprintf('DROP TABLE %s', $table);
            DB::query($sql)->execute();
        }

        Profiler::dump();
    }

    protected function setUp()
    {
        Profiler::section(get_called_class() . '::' . __FUNCTION__);
        parent::setUp();

        /* TODO: 設定ファイルから取得する */
        $dev_prefix = '';
        $test_prefix = 'test_';

        foreach ($this->tables as $table => $params) {
            $from = $dev_prefix . $table;
            $to = $test_prefix . $table;
            $this->copyTable($from, $to);

            if (!empty($params)) {
                $copy = array_shift($params);

                if ($copy) {
                    $limit = array_shift($params);
                    $this->importRows($from, $to, $limit);
                }
            }
        }
    }

    private function copyTable($from, $to)
    {
        if (isset(self::$created_tables[$to])) {
            $sql = sprintf('TRUNCATE TABLE %s', $to);
            $result = DB::query($sql)->execute();
        } else {
            $sql = sprintf('CREATE TABLE IF NOT EXISTS %s LIKE %s', $to, $from);
            $result = DB::query($sql)->execute();
            self::$created_tables[$to] = true;
        }

        return $result;
    }

    private function importRows($from, $to, $limit = null)
    {
        $sql = sprintf('INSERT INTO %s SELECT * FROM %s', $to, $from);

        if ($limit) {
            $sql .= sprintf(' LIMIT %d', $limit);
        }

        return DB::query($sql)->execute();
    }

}
