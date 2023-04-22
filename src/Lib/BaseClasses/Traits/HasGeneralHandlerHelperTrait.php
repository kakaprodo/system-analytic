<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits;


trait HasGeneralHandlerHelperTrait
{
    /**
     * Grab a single value of the provided key from the search fields
     */
    public function getSearchValue($key)
    {
        return $this->data->search_value[$key] ?? null;
    }
}
