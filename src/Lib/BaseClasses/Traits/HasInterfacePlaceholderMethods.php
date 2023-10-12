<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits;

use Kakaprodo\CustomData\CustomData;

trait HasInterfacePlaceholderMethods
{

    /**
     * provide the expected fields that need to be provided
     * as search value
     */
    public function expectedSearchFields(): array
    {
        return [];
    }

    /**
     * Define expected search fields and validate them
     */
    public function expectedSearchFieldsWithValidation(CustomData $customData): array
    {
        return [];
    }
}
