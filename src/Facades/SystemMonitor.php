<?php

namespace Kakaprodo\SystemAnalytic\Facades;

use Illuminate\Support\Facades\Facade;
use Kakaprodo\SystemAnalytic\MonitorCore;

class SystemMonitor extends Facade
{

    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor(): string
    {
        return MonitorCore::class;
    }
}
