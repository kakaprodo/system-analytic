<?php

namespace App\Http\SystemAnalytic;

use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\Data\Base\AnalyticHandlerRegisterBase;

class AnalyticHandlerRegister extends AnalyticHandlerRegisterBase
{
    /**
     * register a key value array of your handlers,
     * where the key is the analytic_type and the value
     * is the actual handler
     */
    public static function handlers(): array
    {
        return [];
    }

    /**
     * Register data that you need to use in your
     * analytic handlers
     */
    public function expectedData(CustomData $data): array
    {
        return [];
    }

    /**
     * Register key that will be ignored when generating 
     * the unique key of the nalytic request
     * (for response caching)
     */
    public function ignorePropertyForKeyGenerator(): array
    {
        return [];
    }

    /**
     * a function that define whether the current authenticated user 
     * is an admin user
     */
    public function scopeUserIsAdmin(AnalyticHandler $handler)
    {
        return null;
    }
}
