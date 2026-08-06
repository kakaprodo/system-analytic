<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data;

use Illuminate\Support\Carbon;
use Kakaprodo\SystemAnalytic\Lib\Data\Base\AnalyticDataBase;
use Kakaprodo\SystemAnalytic\Lib\Shared\HandlerAccessibilityScope;
use Kakaprodo\SystemAnalytic\Utilities\Util;

/**
 * @property string $analytic_type
 * @property string|null $scope_type
 * @property string|Carbon|null $scope_value
 * @property string|Carbon|null $scope_from_date
 * @property string|Carbon|null $scope_to_date
 * @property string|array|null $search_value
 * @property string|boolean|null $boolean_scope_type
 * @property boolean|null $should_export
 * @property string|null $file_type
 * @property string|null $selected_option
 * @property bool|null $should_clear_cache
 * @property bool|null $refresh_persisted_result
 * @property bool|null $scope_is_up_to This will tell the package to filter the scope type from day one up to the provided date
 */
class AnalyticData extends AnalyticDataBase
{
    /**
     * The date column on which we can reference 
     * the scope
     * @var string|null
     */
    public $scopeColumn = null;

    protected function expectedProperties(): array
    {
        return array_merge([
            'analytic_type' => $this->property()->string()->castForValidation(function ($value) {
                return $this->analytic_type = Util::classToKebak($value);
            }),
            'scope_type?' => $this->property()->string(),
            'scope_value?',
            'scope_from_date?',
            'scope_to_date?',
            'search_value?',
            'boolean_scope_type?', // like with_trashed
            'should_export?',
            'file_type?',
            'selected_option?',
            'should_clear_cache?' => $this->property()->bool(),
            'refresh_persisted_result?' => $this->property()->bool(),
            /**
             * This will tell the package to filter the scope type from day one up to the provided date
             */
            'scope_is_up_to?' => $this->property()->bool(false),
        ], $this->handlerRegisterData()->expectedData($this));
    }

    public function boot() {}

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
     * all registerd handlers + the protected ones from accessibilityScope
     */
    public static function handlers(): array
    {
        $handlerRegister = self::handlerRegisterClass();

        $commonHandlers =  $handlerRegister::handlers();

        // register private handlers and merge them with the common ones
        $handlerRegister::registerProtectedHandlers(new HandlerAccessibilityScope());

        $handlers = array_merge($commonHandlers, HandlerAccessibilityScope::getAllProtectedHandlers());

        Util::whenYes($handlers == [], 'You need first to   register a handler before calling it');

        return $handlers;
    }
}
