<?php

namespace Kakaprodo\SystemAnalytic\Monitoring\DataPayload;

use Kakaprodo\SystemAnalytic\Monitoring\DataPayload\MonitorBaseData;


class RequestPayloadData extends MonitorBaseData
{
    protected function expectedProperties(): array
    {
        return [
            ...(parent::expectedProperties()),
            'type' => $this->property()->string(self::PAYLOAD_TYPE_REQUEST),
            'method' => $this->property()->string(),
            'url' => $this->property()->string(),
            'ip' => $this->property()->string(),
            'user?' =>  $this->property()->array(),
            'payload?' => $this->property()->array([]),
            'headers' => $this->property()->array([]),
            'status_code' => $this->property()->number(),
        ];
    }
}


// 'method' => $request->method(),
// 'url' => $request->fullUrl(),
// 'ip' => $request->ip(),
// 'user_id' => optional($request->user())->id,
// 'payload' => $request->except(['password', 'password_confirmation']),
// 'headers' => $request->headers->all(),