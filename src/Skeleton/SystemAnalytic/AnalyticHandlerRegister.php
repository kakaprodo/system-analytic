<?php

namespace App\Http\SystemAnalytic;

use Kakaprodo\SystemAnalytic\Data\Base\AnalyticHandlerRegisterBase;

class AnalyticHandlerRegister extends AnalyticHandlerRegisterBase
{
    public static function handlers(): array
    {
        return [];
    }

    public function expectedData(): array
    {
        return [];
    }

    public function ignoreForKeyGenerator(): array
    {
        return [];
    }
}
