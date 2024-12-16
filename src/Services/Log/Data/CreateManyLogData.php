<?php

namespace Kakaprodo\SystemAnalytic\Services\Log\Data;

use Kakaprodo\SystemAnalytic\Services\Base\Data\BaseData;
use Kakaprodo\SystemAnalytic\Services\Log\Data\CreateLogData;

/**
 * @property array $logs
 */
class CreateManyLogData extends BaseData
{

    protected function expectedProperties(): array
    {
        return [
            'logs' => $this->property()->isArrayOf(CreateLogData::class)->rules([
                'required',
                'array'
            ]),
        ];
    }
}
