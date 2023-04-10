<?php

namespace Kakaprodo\SystemAnalytic;

use App\Utilities\Analytics\Data\AnalyticData;
use App\Utilities\Analytics\Lib\AnalyticGateBase;

class AnalyticGate extends AnalyticGateBase
{

    protected static function registeredHandlers(): array
    {
        return [];
    }

    public function handle(AnalyticData $data)
    {
        $analyticResponse =  $this->detectAndCreateHandler($data);

        return $analyticResponse->format();
    }
}
