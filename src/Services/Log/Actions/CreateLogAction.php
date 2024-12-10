<?php

namespace Kakaprodo\SystemAnalytic\Services\Log\Actions;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\CustomData\Helpers\CustomActionBuilder;
use Kakaprodo\SystemAnalytic\Services\Log\Data\CreateLogData;

class CreateLogAction extends CustomActionBuilder
{
    public function handle(CreateLogData $data)
    {
        if (!$data->shouldStoreLog()) return;

        $inputs = array_merge($data->wrapper('for_db'), [
            'key' => $data->dataKey()
        ]);

        return Util::logModel()::create($inputs);
    }
}
