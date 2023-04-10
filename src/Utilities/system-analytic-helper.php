<?php

use Kakaprodo\SystemAnalytic\Exception\SystemAnalyticException;

use Carbon\Carbon;
use Illuminate\Support\Str;

if (!function_exists('fireErr')) {
    /**
     * build execption instance based on a given message and status
     */
    function fireErr($errorMsg, $status = 400)
    {
        return new SystemAnalyticException($errorMsg, $status);
    }
}

if (!function_exists('whenNot')) {
    /**
     * return error message when the passed statement is false,
     * This function overide status to receive error reason or status
     */
    function whenNot($statement, $message, $status = 400, $reason = null)
    {
        $newStatus = is_numeric($status) ? $status : 400;

        $reason = $reason ?? (is_array($status) ? $status : null);

        if (!$statement) fireErr($message, $newStatus)
            ->reason($reason)
            ->die();
    }
}

if (!function_exists('whenYes')) {
    /**
     * return error message when the passed statement is true
     */
    function whenYes($statement, $message, $status = 400, $reason = null)
    {

        $newStatus = is_numeric($status) ? $status : 400;

        $reason = $reason ?? (is_array($status) ? $status : null);

        if ($statement) fireErr($message, $newStatus)
            ->reason($reason)
            ->die();
    }
}


if (!function_exists('classToKebak')) {

    /**
     * convert a class name to kebak case
     */
    function classToKebak($className)
    {
        return Str::kebab(className($className));
    }
}

if (!function_exists('className')) {
    /**
     * Get the class name  without namespace of a given object
     * @return string
     */
    function className($myClass)
    {
        if (!$myClass) return null;

        $myClass = is_string($myClass) ? $myClass : get_class($myClass);

        $splitedClass = explode('/', str_replace('\\', '/', $myClass));

        return collect($splitedClass)->last();
    }
}

if (!function_exists('parseDate')) {

    /**
     * Parse a string to date with carbon, and catch
     * error when the string is parsable
     */
    function parseDate($date, $inputField = null)
    {
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {

            fireErr($e->getMessage())->die();
        }
    }
}

if (!function_exists('strTitle')) {

    function strTitle($value, $shouldTitle = true)
    {
        if (!$shouldTitle) return $value;

        return (string) Str::title(removeTrait($value), ' ');
    }
}

if (!function_exists('removeTrait')) {

    /**
     * remove character like : _, -
     */
    function removeTrait($myStr, $replace = " ")
    {
        $myStr = Str::replace("_", $replace, $myStr);
        $myStr = Str::replace("-", $replace, $myStr);

        return $myStr;
    }
}

if (!function_exists('callFunction')) {

    /**
     * call a given function if it's callable otherwise return it
     * as a noormal variable
     */
    function callFunction($myFunction, $throwableMsg = null)
    {
        if (is_callable($myFunction)) return $myFunction();

        if ($throwableMsg) fireErr($throwableMsg)->die();

        return $myFunction;
    }
}

if (!function_exists('formatDate')) {

    /**
     * Format a given date with a given format
     */
    function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($date)->format($format);
    }
}
