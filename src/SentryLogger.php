<?php
/*
 * register in  config.neon
 * services:
 *    tracy.logger: SentryLogger
 *
 *
 */


namespace Sitmpcz;

use Tracy\ILogger;
use Sentry;

class SentryLogger implements ILogger
{
    /** @var string[] */
    private $allowedPriority = [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL];

    private $ready = false;

    public function __construct(string $url = '')
    {
        if ($url != '') {
            // is registration OK?
            try {
                Sentry\init(['dsn' => $url]);
                $this->ready = true;
            } catch (Exception $e) {
                // what now?
            }
        }

    }

    public function log($value, $priority = ILogger::INFO)
    {
        if ($this->ready) {
            if (!in_array($priority, $this->allowedPriority, true)) {
                return;
            }
            Sentry\captureException($value);
            //\Sentry\captureMessage($value);
        }
    }
}
