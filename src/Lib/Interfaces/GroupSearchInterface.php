<?php

namespace Kakaprodo\SystemAnalytic\Lib\Interfaces;

interface GroupSearchInterface
{
    /**
     * provide the expected fields that need to be provided
     * as search value
     */
    public function expectedSearchFields(): array;
}
