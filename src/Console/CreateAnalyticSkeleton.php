<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Console\Command;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class CreateAnalyticSkeleton extends Command
{
    protected $hidden = true;

    protected $signature = 'system-analytic:install';

    protected $description = 'Create the System Analytic Skeleton';

    public function handle()
    {
        if (!$this->skeletonExists()) {
            $this->info('START Creating Analytics Hub...');
            $this->publishAnalyticHub();
            $this->info('Analytic Skeleton Successfully created');
        }
    }

    private function skeletonExists()
    {
        return file_exists(Util::hubFolder());
    }

    private function publishAnalyticHub()
    {
        $params = [
            '--provider' => "Kakaprodo\SystemAnalytic\SystemAnalyticServiceProvider",
            '--tag' => "analytic-skeleton"
        ];

        $this->call('vendor:publish', $params);
    }
}
