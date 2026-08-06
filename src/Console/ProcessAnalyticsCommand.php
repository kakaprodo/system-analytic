<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Console\Command;
use Kakaprodo\SystemAnalytic\AnalyticGate;

class ProcessAnalyticsCommand extends Command
{

    protected $signature = 'system-analytic:process 
                            {analytic-type : The type or name of the analytic to process} 
                            {--scope-type= : provide the period type} 
                            {--scope-value= : Optional scope value}
                            {--search-value= : Optional search term}
                            {--selected-option= : Optional selected option}
                            {--scope-is-up-to= : Optional support of up to scope}
                            ';


    protected $description = 'A command to run a given analytic handler';

    public function handle()
    {
        $analyticType = $this->argument('analytic-type');
        $scopeType = $this->option('scope-type');
        $scopeValue = $this->option('scope-value');
        $searchValue = $this->option('search-value');
        $selectedOption = $this->option('selected-option');
        $isUpTo = $this->option('scope-is-up-to');

        $result = AnalyticGate::process([
            'analytic_type' =>  $analyticType,
            'scope_type' => $scopeType,
            'scope_value' => $scopeValue,
            'search_value' => $searchValue,
            'selected_option' =>  $selectedOption,
            'scope_is_up_to' =>  $isUpTo,
            'should_clear_cache' => true
        ]);

        $result = $result['result'] ??  $result;

        dump($result);
    }
}
