<?php

namespace Kakaprodo\SystemAnalytic\Monitoring\DataPayload;

use Kakaprodo\SystemAnalytic\Monitoring\DataPayload\MonitorBaseData;


class ReportPayloadData extends MonitorBaseData
{
    protected function expectedProperties(): array
    {
        return [
            ...(parent::expectedProperties()),
            'type' => $this->property()->string(self::PAYLOAD_TYPE_REPORT),
            'message' => $this->property()->string(),
            'code?' => $this->property()->string(),
            'file' => $this->property()->string(),
            'line' => $this->property()->string(),
            'trace' => $this->property()->string(),
            'status_code?' => $this->property()->string(),
        ];
    }

    public function boot()
    {
        $this->status_level = $this->resolveLevel();
    }

    public function resolveLevel(): string
    {
        if ($this->status_code) {
            $status = $this->status_code;

            if ($status >= 500) return 'error';
            if ($status >= 400) return 'warning';

            return 'info';
        }

        return 'critical'; // unexpected system failure
    }
}
