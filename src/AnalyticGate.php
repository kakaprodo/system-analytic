<?php

namespace Kakaprodo\SystemAnalytic;

use Kakaprodo\SystemAnalytic\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\AnalyticGateBase;

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
