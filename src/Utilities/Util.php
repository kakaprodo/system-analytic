<?php

namespace Kakaprodo\SystemAnalytic\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Kakaprodo\SystemAnalytic\AnalyticGate;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;
use Kakaprodo\SystemAnalytic\Lib\ExportHub\Base\ExportHubBase;
use Kakaprodo\SystemAnalytic\Exception\SystemAnalyticException;

class Util
{
    /**
     * build execption instance based on a given message and status
     */
    public static function fireErr($errorMsg, $status = 400)
    {
        return new SystemAnalyticException($errorMsg, $status);
    }

    /**
     * return error message when the passed statement is false
     */
    public static function whenNot($statement, $message, $status = 400)
    {
        if (!$statement) self::fireErr($message, $status)->die();
    }

    /**
     * return error message when the passed statement is true
     */
    public static function whenYes($statement, $message, $status = 400)
    {
        if ($statement) self::fireErr($message, $status)->die();
    }

    /**
     * convert a class name to kebak case
     */
    public static function classToKebak($className)
    {
        return Str::kebab(self::className($className));
    }

    /**
     * Get the class name  without namespace of a given object
     * @return string
     */
    public static function className($myClass)
    {
        if (!$myClass) return null;

        $myClass = is_string($myClass) ? $myClass : get_class($myClass);

        $splitedClass = explode('/', str_replace('\\', '/', $myClass));

        return collect($splitedClass)->last();
    }

    /**
     * Parse a string to date with carbon, and catch
     * error when the string is parsable
     */
    public static function parseDate($date, $inputField = null)
    {
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {

            self::fireErr($e->getMessage())->die();
        }
    }

    public static function strTitle($value, $shouldTitle = true)
    {
        if (!$shouldTitle) return $value;

        return (string) Str::title(self::removeTrait($value), '');
    }

    /**
     * remove character like : _, -
     */
    public static function removeTrait($myStr, $replace = "")
    {
        $myStr = Str::replace("_", $replace, $myStr);
        $myStr = Str::replace("-", $replace, $myStr);

        return $myStr;
    }

    /**
     * call a given public static function if it's callable otherwise return it
     * as a noormal variable
     */
    public static function callFunction($myFunction, $throwableMsg = null)
    {
        if (is_callable($myFunction)) return $myFunction();

        if ($throwableMsg) self::fireErr($throwableMsg)->die();

        return $myFunction;
    }

    /**
     * Format a given date with a given format
     */
    public static function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * Grab folder from a given path string
     */
    public static function folderFromPath($path)
    {
        $folderPath = explode('app/', $path);

        return $folderPath[1] ?? '';
    }

    /**
     * the folder path where the analytic handler are located
     * in the project in which the package is installed
     */
    public static function hubFolder()
    {
        return config('system-analytic.analytic_path') . '/' . config('system-analytic.folder_name');
    }

    /**
     * path Where the from validation class will be created
     */
    public static function validationFolder()
    {
        return config('system-analytic.form_validation_path') . '/' . config('system-analytic.folder_name');
    }

    /**
     * all the registered analytic handlers
     */
    public static function allHandlers()
    {
        return AnalyticGate::allHandlers();
    }

    /**
     * Check if the scope should be required 
     */
    public static function  shouldRequireScope($analyticType)
    {
        $handlerClass = AnalyticGate::handlerClass($analyticType);

        return $handlerClass::$scopeIsRequired === true;
    }

    /**
     * provide date validation rule based on scope type 
     */
    public static function detectDateFormat($scopeType)
    {
        if (!AnalyticGate::isPeriodicTypes($scopeType)) return;

        $format =  [
            AnalyticFilterHub::TYPE_FIXED_MONTH => 'Y-m',
            AnalyticFilterHub::TYPE_FIXED_YEAR => 'Y',
            AnalyticFilterHub::TYPE_RANGE_DATE => 'Y-m-d',
            AnalyticFilterHub::TYPE_RANGE_MONTH => 'Y-m',
            AnalyticFilterHub::TYPE_RANGE_YEAR => 'Y',
        ][$scopeType] ?? 'Y-m-d';

        return 'date_format:' . $format;
    }

    /**
     * Grab supported scope type of a given handler
     */
    public static function handlerScopeTypes($analyticType)
    {
        return AnalyticGate::detectScopeTypes($analyticType);
    }

    /**
     * check if the scope type is in the group of fixed
     * scope type(like fixed_month,...)
     */
    public static function isFixedScopeType($scopeType)
    {
        return  AnalyticGate::isFixedScopeType($scopeType);
    }

    /**
     * check if the provided scope is of range type
     * like(range_year, range_month,...)
     */
    public static function isRangeScopeType($scopeType)
    {
        return  AnalyticGate::isRangeScopeType($scopeType);
    }

    /**
     * get all the scope types supported by boolean filtering
     */
    public static function getBooleanScopeTypes($analyticType)
    {
        return  AnalyticGate::detectBooleanScopeTypes($analyticType);
    }

    /**
     * get all the files extension that can be exported
     */
    public static function exportSupportedFiles()
    {
        return  ExportHubBase::$supporteFiles;
    }
}
