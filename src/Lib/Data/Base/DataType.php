<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\CustomData\Exceptions\MissedRequiredPropertyException;
use Kakaprodo\SystemAnalytic\Lib\Data\Base\AnalyticHandlerRegisterBase;

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

    /**
     * Throw exception when a field does not exist on customdata
     */
    public function throwWhenFieldAbsent($fieldName, $msg = null)
    {
        if ($this->$fieldName !== null) return;

        $msg = $msg ?? "The {$fieldName} field is required";

        if (method_exists($this, 'errorHandler')) {
            $this->errorHandler($msg);
        }

        throw new MissedRequiredPropertyException($msg ?? "The {$fieldName} field is required");
    }

    protected function errorHandler($errorMessage)
    {
        Util::fireErr($errorMessage)->die();
    }
}
