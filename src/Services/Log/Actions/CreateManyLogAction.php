<?php

namespace Kakaprodo\SystemAnalytic\Services\Log\Actions;

use Kakaprodo\CustomData\Helpers\CustomActionBuilder;
use Kakaprodo\SystemAnalytic\Services\Log\Data\CreateManyLogData;

class CreateManyLogAction extends CustomActionBuilder
{
    public function handle(CreateManyLogData $data)
    {
        foreach ($data->logs as $logData) {
            CreateLogAction::process($logData);
        }
    }
}
