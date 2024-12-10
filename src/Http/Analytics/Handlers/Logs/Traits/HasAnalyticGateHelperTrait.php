<?php

namespace Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\Traits;

use Kakaprodo\SystemAnalytic\Utilities\Util;

trait HasAnalyticGateHelperTrait
{
    protected function logTableName()
    {
        return (new (Util::logModel()))->getTable();
    }
    public function addCommonFilterToQuery($q) {}
}
