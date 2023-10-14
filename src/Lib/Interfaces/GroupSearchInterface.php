<?php

namespace Kakaprodo\SystemAnalytic\Lib\Interfaces;

use Kakaprodo\CustomData\CustomData;

interface GroupSearchInterface
{
    /**
     * provide the expected fields that need to be provided
     * as search value
     */
    public function expectedSearchFields(): array;

    /**
     * Define expected search fields and validate them
     */
    public function expectedSearchFieldsWithValidation(CustomData $customData): array;
}
