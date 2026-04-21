<?php

namespace Kakaprodo\SystemAnalytic\Monitoring\Events;

use Illuminate\Database\Events\QueryExecuted;
use Kakaprodo\SystemAnalytic\MonitorCore;
use Kakaprodo\SystemAnalytic\Monitoring\DataPayload\QueryPayloadData;
use Throwable;

/**
 * @internal
 */
final class QueryExecutedListener
{

    public function __construct(
        private MonitorCore $monitor,
    ) {
        //
    }

    public function __invoke(QueryExecuted $event): void
    {
        try {
            //dd($event);
            QueryPayloadData::make([
                'sql' => $event->sql,
                'bindings' => $event->bindings,
                'time' => $event->time,
                'connection_name' =>  $event->connectionName,
                'monitor' => $this->monitor
            ]);
        } catch (Throwable $e) {
            // $this->monitor->report($e, handled: true);
        }
    }
}
