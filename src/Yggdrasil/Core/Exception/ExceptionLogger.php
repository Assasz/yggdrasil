<?php

namespace Yggdrasil\Core\Exception;

/**
 * Class ExceptionLogger
 *
 * @package Yggdrasil\Core\Exception
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class ExceptionLogger
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
     * @return ExceptionLogger
     */
    public function setLogPath(string $path): ExceptionLogger
    {
        $this->logPath = $path;

        return $this;
    }

    /**
     * Logs given throwable object (exception, error) in log output file
     *
     * @param \Throwable $throwable
     */
    public function log(\Throwable $throwable)
    {
        $date = new \DateTime();

        $log = "[$date] {$throwable->getMessage()} at line {$throwable->getLine()} in {$throwable->getFile()}";

        $handle = fopen($this->logPath, 'a');
        fwrite($handle, $log);
        fclose($handle);
    }
}
