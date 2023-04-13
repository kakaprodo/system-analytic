<?php

namespace Kakaprodo\SystemAnalytic\Data;

use Kakaprodo\SystemAnalytic\Data\Base\AnalyticDataBase;

class AnalyticData extends AnalyticDataBase
{

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
        ], $this->handlerRegisterClass()->ignoreForKeyGenerator());
    }

    /**
     * all registerd handlers
     */
    public static function handlers(): array
    {
        $handlerRegisterClass = config('system-analytic.handler_register');

        return $handlerRegisterClass::handlers();
    }
}
