<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits;

use Illuminate\Support\Str;


trait HasGeneralHandlerHelperTrait
{
    /**
     * Grab a single value of the provided key from the search fields
     */
    public function getSearchValue($key)
    {
        return $this->data->search_value[$key] ?? null;
    }

    /**
     * replace a {placeholder} in a string
     */
    public function replacePlaceholders($string, $data = [], $defaultName = null)
    {
        return preg_replace_callback('/\{([^\}]+)\}/', function ($matches) use ($data, $defaultName) {
            $key = $matches[1];
            return isset($data[$key]) ? $data[$key] : $defaultName;
        }, $string);
    }

    /**
     * Check if the handler supports multiple scope 
     * columns
     */
    public function supportMultipleScopeColumns()
    {
        return is_array($this->scopeColumn) || Str::contains($this->scopeColumn, '|');
    }
}
