<?php

namespace Sitmpcz;

use Sentry;
use stdClass;

class SentryPerformance
{
    public static function startPerformaceMonitoring(string $name, string $context): object
    {
        // tady bych si mel zjistit jestli je monitoring zapnuty nebo vypnuty a taky s jakou pravdepodobnosti mam davat do sentry
        // zatim jedu jen na testovacim prostredí
        // pozor v konfigurace sentry mam nastveno, ze jede jen pokud neni zapnuty debug rezim (tj neni videt tracy bar)
        $sentryPerformance = new stdClass();
        // https://docs.sentry.io/platforms/php/performance/
        $sentryTransactionContext = new Sentry\Tracing\TransactionContext();
        $sentryTransactionContext->setName($name);
        $sentryTransactionContext->setOp('http.caller');
        $sentryPerformance->transaction = Sentry\startTransaction($sentryTransactionContext);


        $sentrySpanContext = new Sentry\Tracing\SpanContext();
        $sentrySpanContext->setOp($context);
        $sentryPerformance->child = $sentryPerformance->transaction->startChild($sentrySpanContext);
        return $sentryPerformance;
    }

    public static function endPerformaceMonitoring(object $sentryPerformance): void
    {
        //
        if (isset($sentryPerformance->child)) {
            $sentryPerformance->child->finish();
        }
        if (isset($sentryPerformance->transaction)) {
            $sentryPerformance->transaction->finish();
        }
    }
}
