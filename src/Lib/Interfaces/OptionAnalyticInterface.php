<?php

namespace Kakaprodo\SystemAnalytic\Lib\Interfaces;

interface OptionAnalyticInterface
{
    /**
     * The suppoorted options of a given analytic 
     * class
     */
    public function options(): array;
}
