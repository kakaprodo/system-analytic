<?php

namespace Kakaprodo\SystemAnalytic\Services\Computation\Actions;

use Kakaprodo\CustomData\Helpers\CustomActionBuilder;
use Kakaprodo\SystemAnalytic\Services\Computation\Data\CreateAnalyticComputationData;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class CreateAnalyticComputationAction extends CustomActionBuilder
{
    public function handle(CreateAnalyticComputationData $data)
    {
        return Util::computationModel()::firstOrCreate([
            'data_key' => $data->dataKey()
        ], $data->wrapper('for_db'));
    }
}
