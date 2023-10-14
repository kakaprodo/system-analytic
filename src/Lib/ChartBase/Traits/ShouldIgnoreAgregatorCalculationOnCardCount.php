<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase\Traits;


trait ShouldIgnoreAgregatorCalculationOnCardCount
{
    protected function builtResult()
    {
        return $this->query->get();
    }
}
