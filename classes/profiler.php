<?php

/**
 * Profiler
 *
 * @author kawamurayuto
 */
class Profiler extends \Fuel\Core\Profiler
{

    protected static $igonore_log_pattern = '';
    protected static $logs = [];

    public static function dump()
    {
        echo '> logs: ';
        print_r(static::$logs);
    }

    public static function push_log($content)
    {
        static::$logs[(string) microtime(true)] = $content;
    }

    public static function set_ignore_log_pattern($pattern)
    {
        static::$igonore_log_pattern = $pattern;
    }

    public static function section($name)
    {
        static::push_log(str_pad($name, '40', '*', STR_PAD_LEFT) . str_repeat('*', 40));
    }

    public static function start($dbname, $sql, $stacktrace = array())
    {
        $igonore_log_pattern = static::$igonore_log_pattern;
        if ($igonore_log_pattern === '' || !preg_match($igonore_log_pattern, $sql)) {
            static::push_log($sql);
        }
        parent::start($dbname, $sql, $stacktrace);
    }

}
