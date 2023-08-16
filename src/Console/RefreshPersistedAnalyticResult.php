<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Console\Command;
use Kakaprodo\SystemAnalytic\AnalyticGate;

class RefreshPersistedAnalyticResult extends Command
{
    protected $hidden = true;

    protected $signature = 'system-analytic:refresh {analytic-type} {persistence-group?}';

    protected $description = 'A command to refresh all the persisted result for a given analytic handler by providing its type or class name';

    public function handle()
    {
        $analyticType = $this->argument('analytic-type');
        $persistenceGroup = $this->argument('persistence-group');

        $this->warn('Start refreshing...');

        AnalyticGate::refreshPersistedResult(
            $analyticType,
            $persistenceGroup,
            function ($analyticReport) {
                $this->info("Analytic {$analyticReport->analytic_type}, with scope: {$analyticReport->analytic_data['scope_type']} Refreshed");
            }
        );

        $this->info('Done...');
    }
}
