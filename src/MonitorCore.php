<?php

namespace Kakaprodo\SystemAnalytic;

use Kakaprodo\SystemAnalytic\Monitoring\DataPayload\MonitorBaseData;
use Kakaprodo\SystemAnalytic\Monitoring\DataPayload\ReportPayloadData;
use Throwable;

class MonitorCore
{
    static $collectedPayload = [];

    public function __construct(
        public string $uuid
    ) {}

    public function collect(MonitorBaseData $data)
    {
        MonitorCore::$collectedPayload = array_merge(
            MonitorCore::$collectedPayload,
            $data->except(['monitor'])
        );

        return $this;
    }

    public function report(Throwable $e)
    {
        ReportPayloadData::make([
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'status_code' => method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : 500,
        ]);

        return $this;
    }
}


// should have a token
// should have a uuid
// should have the server
// should be able to collect any kind of data
