<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits;


/**
 * @property \Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData $data
 */
trait HasMethodCallingTrait
{
    /**
     * Use the registered scope methods
     */
    public function __call($method, $arguments)
    {
        return $this->data->handlerRegisterData()->$method(...[$this, ...$arguments]);
    }
}
