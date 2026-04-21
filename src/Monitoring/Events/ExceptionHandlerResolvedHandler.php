<?php

namespace Kakaprodo\SystemAnalytic\Monitoring\Events;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
use Kakaprodo\SystemAnalytic\MonitorCore;
use Throwable;

/**
 * @internal
 */
final class ExceptionHandlerResolvedHandler
{
    public function __construct(
        private MonitorCore $monitor,
    ) {
        //
    }

    public function __invoke(ExceptionHandler $handler): void
    {
        try {
            if ($handler instanceof Handler) {
                $handler->reportable(fn(\Throwable $e) => dd($e));
            }
        } catch (Throwable $e) {
        }
    }
}
