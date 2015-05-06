<?php namespace Vdbf\SiteMapper\Support;

use Psr\Log\LogLevel;

trait LoggerHelperTrait
{
    /**
     * @param $message
     * @param string $level
     * @param array $context
     */
    public function log($message, $level = LogLevel::DEBUG, $context = [])
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
}