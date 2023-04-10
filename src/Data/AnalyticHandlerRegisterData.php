<?php

namespace Kakaprodo\SystemAnalytic\Data;

use Kakaprodo\SystemAnalytic\Data\DataType;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\AnalyticHandlerRegisterInterface;

class AnalyticHandlerRegisterData extends DataType implements AnalyticHandlerRegisterInterface
{
    public function register(): array
    {
        return [];
    }
}
