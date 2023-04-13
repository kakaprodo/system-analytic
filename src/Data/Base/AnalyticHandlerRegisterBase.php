<?php

namespace Kakaprodo\SystemAnalytic\Data\Base;

use Kakaprodo\SystemAnalytic\Data\Base\DataType;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\AnalyticHandlerRegisterInterface;

abstract class AnalyticHandlerRegisterBase extends DataType implements AnalyticHandlerRegisterInterface
{
    protected function expectedProperties(): array
    {
        return [];
    }
}
