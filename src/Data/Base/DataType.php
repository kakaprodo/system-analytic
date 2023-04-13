<?php

namespace Kakaprodo\SystemAnalytic\Data\Base;

use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Data\Base\AnalyticHandlerRegisterBase;

abstract class DataType extends CustomData
{
    /**
     * Class where handlers, expected data and other macroMethod 
     * will be registered
     * 
     * @var AnalyticHandlerRegisterBase
     */
    protected $handlerRegisterData;


    protected function expectedProperties(): array
    {
        return [];
    }

    /**
     * the class where handler, expected Data and other macroMethod 
     * will are registered
     */
    public function handlerRegisterClass(): AnalyticHandlerRegisterBase
    {
        if ($this->handlerRegisterData) return $this->handlerRegisterData;

        $handlerRegisterClass = config('system-analytic.handler_register');

        return $this->handlerRegisterData = $handlerRegisterClass::make([]);
    }
}
