<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Console\Command;

class CreateAnalyticSkeleton extends Command
{
    protected $hidden = true;

    protected $signature = 'system-analytic:skeleton';

    protected $description = 'Create the System Analytic Skeleton';

    public function handle()
    {
        $this->info('START Creating Analytics Hub...');

        if (!$this->skeletonExists()) {
            $this->publishConfiguration();
            $this->info('Analytic Skeleton Successfully created');
        }
    }

    private function skeletonExists()
    {
        return file_exists(config('system-analytic.analytic_path') . '/SystemAnalytic');
    }

    private function publishConfiguration()
    {
        $params = [
            '--provider' => "Kakaprodo\SystemAnalytic\SystemAnalyticServiceProvider",
            '--tag' => "analytic-skeleton"
        ];

        $this->call('vendor:publish', $params);
    }
}
