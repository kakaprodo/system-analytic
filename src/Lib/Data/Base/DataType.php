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

    protected $originalData;

    protected $customDataKey;

    protected $filterValueHub;

    /**
     * Collect data unique key from the fresh provided data
     */
    public function dataKey()
    {
        if ($this->customDataKey) return $this->customDataKey;

        return $this->customDataKey = parent::dataKey();
    }

    /**
     * the original data before any modification
     */
    public function setOriginalData()
    {
        $this->originalData = $this->all();

        // load the original key
        $this->dataKey();
    }

    /**
     * get the original inputed data that came right from 
     * the request
     */
    public function getOriginalData()
    {
        return $this->originalData;
    }

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
