<?php

namespace Kakaprodo\SystemAnalytic\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Str;
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
        return Str::kebab(className($className));
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

        return (string) Str::title(removeTrait($value), '');
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
}
