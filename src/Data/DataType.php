<?php

namespace Kakaprodo\SystemAnalytic\Data;

use Kakaprodo\CustomData\CustomData;

abstract class DataType extends CustomData
{
    protected function expectedProperties(): array
    {
        return [];
    }
}
