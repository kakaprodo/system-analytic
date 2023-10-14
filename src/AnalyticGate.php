<?php

namespace Kakaprodo\SystemAnalytic;

use Kakaprodo\SystemAnalytic\Lib\AnalyticGateBase;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;

class AnalyticGate extends AnalyticGateBase
{

    protected static function registeredHandlers(): array
    {
        return AnalyticData::handlers();
    }

    public function handle(AnalyticData $data)
    {
        $analyticResponse =  $this->detectAndCreateHandler($data);

        return $analyticResponse->format();
    }
}
