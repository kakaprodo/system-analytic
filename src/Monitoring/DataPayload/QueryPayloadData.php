<?php

namespace Kakaprodo\SystemAnalytic\Monitoring\DataPayload;

use Kakaprodo\SystemAnalytic\Monitoring\DataPayload\MonitorBaseData;


class QueryPayloadData extends MonitorBaseData
{
    protected function expectedProperties(): array
    {
        return [
            ...(parent::expectedProperties()),
            'type' => $this->property()->string(self::PAYLOAD_TYPE_QUERY),
            'sql' => $this->property()->string(),
            'bindings?' => $this->property()->array([]),
            'time' => $this->property()->number(),
            'connection_name' => $this->property()->string('mysql')
        ];
    }
}
