<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Lib\Interfaces\AnalyticHandlerRegisterInterface;
use Kakaprodo\SystemAnalytic\Utilities\Util;

abstract class AnalyticHandlerRegisterBase implements AnalyticHandlerRegisterInterface
{
    /**
     * Handle the scope method calling
     */
    public function __call($name, $arguments)
    {
        $appropriateMethod = "scope" . (Util::strTitle($name));

        if (!method_exists($this, $appropriateMethod)) {
            throw Util::fireErr("Method {$name} does not exists");
        }

        return $this->$appropriateMethod(...$arguments);
    }

    public function __get($name)
    {
        return null;
    }
}
