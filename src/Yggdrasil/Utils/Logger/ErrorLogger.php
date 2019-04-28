<?php

namespace Yggdrasil\Utils\Logger;

/**
 * Class ErrorLogger
 *
 * @package Yggdrasil\Utils\Logger
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class ErrorLogger
{
    /**
     * Log output file path
     *
     * @var string
     */
    private $logPath;

    /**
     * Sets log output file path
     *
     * @param string $path
     * @return ErrorLogger
     */
    public function setLogPath(string $path): ErrorLogger
    {
        $this->logPath = $path;

        return $this;
    }

    /**
     * Logs given throwable object (exception, error) in log output file
     *
     * @param \Throwable $throwable
     */
    public function log(\Throwable $throwable): void
    {
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $log = "[$date] {$throwable->getMessage()} at line {$throwable->getLine()} in {$throwable->getFile()}" . PHP_EOL;
        $dirname = dirname($this->logPath);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0755);
        }

        $handle = fopen($this->logPath, 'a');
        fwrite($handle, $log);
        fclose($handle);
    }
}
