<?php

namespace Kakaprodo\SystemAnalytic\Lib\Validation;

use Kakaprodo\SystemAnalytic\Lib\Interfaces\AdminInterface;
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

        return $this;
    }

    private function validateOptionableHandlers()
    {
        $this->data->throwWhenFieldAbsent('selected_option');

        $supportedOptions = $this->options();

        // check if the provided options matches with the one registered on handler
        $selectedOptions = explode(',', $this->data->selected_option);
        foreach ($selectedOptions as $option) {
            whenNot(
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
        whenNot(
            $this->userIsAdmin(),
            'Only admin user can access on this analytic:' . $this->data->analytic_type
        );
    }
}
