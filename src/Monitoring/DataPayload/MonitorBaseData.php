<?php

namespace Kakaprodo\SystemAnalytic\Monitoring\DataPayload;

use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\MonitorCore;

/**
 * @property MonitorCore $monitor
 */
abstract class MonitorBaseData extends CustomData
{
    const PAYLOAD_TYPE_QUERY = 'query';
    const PAYLOAD_TYPE_REQUEST = 'request';
    const PAYLOAD_TYPE_REPORT = 'report';

    protected function expectedProperties(): array
    {
        return [
            'uuid' => $this->property()->string($this->monitor->uuid),
            'environment' => $this->property()->string(app()->environment()),
        ];
    }

    /** override the custom-data life cycle */
    protected function handleLifecycle(?callable $beforeBoot = null)
    {
        parent::handleLifecycle($beforeBoot);

        $this->monitor->collect($this);
    }
}
