<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Lib\Interfaces\AnalyticHandlerRegisterInterface;
use Kakaprodo\SystemAnalytic\Utilities\Util;

abstract class AnalyticHandlerRegisterBase implements AnalyticHandlerRegisterInterface
{
    /**
     * Handle the macro method calling
     */
    public function __call($name, $arguments)
    {
        $appropriateMethod = "macro" . (Util::strTitle($name));

        if (!method_exists($this, $appropriateMethod)) {
            throw Util::fireErr("Method {$name} does not exists");
        }

        return $this->$appropriateMethod(...$arguments);
    }

    /**
     * The additional request rules to use 
     * in the integrated RequestForm validation
     */
    public function requestRules(): array
    {
        return [];
    }

    public function __get($name)
    {
        return null;
    }
}
