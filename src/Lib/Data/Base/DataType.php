<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Exception;
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
     * The class namespace of handlers register
     */
    public static function handlerRegisterClass()
    {
        $handlerRegisterClass = config('system-analytic.handler_register');

        if (class_exists($handlerRegisterClass)) return $handlerRegisterClass;

        throw new Exception($handlerRegisterClass . " not found, make sure you have resolved the namespace of the handler_register in the system-analytic.php config file");
    }

    /**
     * the class where handler, expected Data and other macroMethod 
     * will are registered
     */
    public function handlerRegisterData(): AnalyticHandlerRegisterBase
    {
        if ($this->handlerRegisterData) return $this->handlerRegisterData;

        $register = self::handlerRegisterClass();

        return $this->handlerRegisterData = new $register();
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
