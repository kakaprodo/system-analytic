<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Lib\Data\Base\DataType;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\AnalyticHandlerRegisterInterface;

abstract class AnalyticHandlerRegisterBase extends DataType implements AnalyticHandlerRegisterInterface
{
    protected function expectedProperties(): array
    {
        return [];
    }
}
