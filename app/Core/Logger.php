<?php

namespace App\Core;

class Logger
{
    /**
     * Path to log file
     *
     * @var string
     */
    private static $logPath = __DIR__ . "/../../storage/logs/log.txt";

    /**
     * Append string to log file
     *
     * @param string $content
     * @return void
     */
    public static function write($content)
    {
        $date = new \DateTime();
        $dateFormat = date_format($date, 'Y-m-d H:i:s');
        $line = "[$dateFormat]: $content\n";
        file_put_contents(self::$logPath, $line, FILE_APPEND | LOCK_EX);
    }
}
