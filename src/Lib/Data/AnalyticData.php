<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data;

use Kakaprodo\SystemAnalytic\Lib\Data\Base\AnalyticDataBase;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class AnalyticData extends AnalyticDataBase
{

    const SCOPE_CATEGORY_HOUR = 'hour';
    const SCOPE_CATEGORY_MONTH = 'month';
    const SCOPE_CATEGORY_YEAR = 'year';

    /**
     * The date column on which we can reference 
     * the scope
     */
    public $scopeColumn = null;

    protected function expectedProperties(): array
    {
        return array_merge([
            'analytic_type' => $this->dataType()->string(),
            'scope_type?' => $this->dataType()->string(),
            'scope_value?',
            'scope_from_date?',
            'scope_to_date?',
            'search_value?',
            'boolean_scope_type?', // like with_trashed
            'should_export?',
            'file_type?',
            'selected_option?',
            'should_clear_cache?' => $this->dataType()->bool()
        ], $this->handlerRegisterClass()->expectedData());
    }

    public function ignoreForKeyGenerator(): array
    {
        return array_merge([
            'should_clear_cache',
            'file_type',
            'should_export'
        ], $this->handlerRegisterClass()->ignorePropertyForKeyGenerator());
    }

    /**
     * all registerd handlers
     */
    public static function handlers(): array
    {
        $handlerRegisterClass = config('system-analytic.handler_register');

        $handlers =  $handlerRegisterClass::handlers();

        Util::whenYes($handlers == [], 'You need first to register a handler before calling it');

        return $handlers;
    }
}
