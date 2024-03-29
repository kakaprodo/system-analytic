<?php

namespace Kakaprodo\SystemAnalytic\Lib\Validation;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\CustomData\Helpers\VirtualCustomData;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\AdminInterface;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\GroupSearchInterface;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\OptionAnalyticInterface;


trait HasAnalyticInterfaceValidationTrait
{

    /**
     * Validate request data based on the implemented interfaces
     * on analytic handler
     */
    protected function validateHandlerInterface()
    {
        if ($this instanceof OptionAnalyticInterface) $this->validateOptionableHandlers();

        if ($this instanceof AdminInterface) $this->validateUserIsAdmin();

        if ($this instanceof GroupSearchInterface) $this->validateSearchValue();

        return $this;
    }

    private function validateOptionableHandlers()
    {
        if (!$this->defaultSelectedOption)  $this->data->throwWhenFieldAbsent('selected_option');

        $supportedOptions = $this->options();
        $this->data->selected_option = $this->data->selected_option ?? $this->defaultSelectedOption;

        // check if the provided options matches with the one registered on handler
        $selectedOptions = explode(',', $this->data->selected_option);
        foreach ($selectedOptions as $option) {
            Util::whenNot(
                in_array($option, $supportedOptions),
                "The selected option {$option} should be one of : " . implode(',', $supportedOptions)
            );
        }

        // support multiple selection
        $this->data->support_multiple_selection = count($selectedOptions) > 1;

        if ($this->data->support_multiple_selection) {
            $this->data->options_chose = $selectedOptions;
        }
    }

    private function validateUserIsAdmin()
    {
        Util::whenNot(
            $this->userIsAdmin(),
            'Only admin user can access on this analytic:' . $this->data->analytic_type
        );
    }

    /**
     * check if the provided search value is an array
     * and has the expected data
     */
    private function validateSearchValue()
    {
        if (!$this->data->search_value) {
            if (!$this->shouldValidWhenSearchTermAbsent) return;
        }

        $this->data->throwWhenFieldAbsent('search_value');

        $searchValue = $this->data->search_value;

        $expectedSearchFields =  $this->expectedSearchFields();

        $applyVirtualValidation = $expectedSearchFields == [];

        if ($applyVirtualValidation) return $this->applyVirtualValidationOnGroupSearchFields();

        Util::whenYes($expectedSearchFields == [], "You need to register the expected search fields");

        $errorMsg = 'The search_value should be an array containing one of these keys: ' . implode(',', $expectedSearchFields);

        Util::whenNot(is_array($searchValue), $errorMsg);

        $onlyExpectedSearchFields = Arr::only($searchValue, $expectedSearchFields);

        Util::whenYes($onlyExpectedSearchFields == [], $errorMsg);
    }

    private function applyVirtualValidationOnGroupSearchFields()
    {
        Util::whenNot(is_array($this->data->search_value), "The search value should be an array");

        VirtualCustomData::check(
            fn ($customData) => $this->expectedSearchFieldsWithValidation($customData),
            $this->data->search_value,
            function ($errorMessage) {
                Util::fireErr($errorMessage)->withData([
                    'reason' => [
                        'analytic' => basename(static::class),
                        'hint' => 'Check the values you have provided in the request search_value'
                    ]
                ])->die();
            }
        );

        return true;
    }
}
