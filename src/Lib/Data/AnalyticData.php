<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\Base\AnalyticDataBase;

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
            'analytic_type' => $this->dataType()->string()->castForValidation(function ($value) {
                return $this->analytic_type = Util::classToKebak($value);
            }),
            'scope_type?' => $this->dataType()->string(),
            'scope_value?',
            'scope_from_date?',
            'scope_to_date?',
            'search_value?',
            'boolean_scope_type?', // like with_trashed
            'should_export?',
            'file_type?',
            'selected_option?',
            'should_clear_cache?' => $this->dataType()->bool(),
            'refresh_persisted_result?' => $this->dataType()->bool(),
        ], $this->handlerRegisterData()->expectedData($this));
    }

    public function boot()
    {
        // set the inputed data before modification
        return $this->setOriginalData();
    }

    public function ignoreForKeyGenerator(): array
    {
        return array_merge([
            'should_clear_cache',
            'file_type',
            'should_export',
            'refresh_persisted_result',
        ], $this->handlerRegisterData()->ignorePropertyForKeyGenerator($this));
    }

    /**
     * all registerd handlers
     */
    public static function handlers(): array
    {
        $handlers =  self::handlerRegisterClass()::handlers();

        Util::whenYes($handlers == [], 'You need first to register a handler before calling it');

        return $handlers;
    }
}
