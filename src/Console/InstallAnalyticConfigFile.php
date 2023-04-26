<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Console\Command;

class InstallAnalyticConfigFile extends Command
{
    protected $signature = 'system-analytic:config';

    protected $description = 'Install the System Analytic package';

    protected $configFileName = "system-analytic.php";

    public function handle()
    {
        $this->info('START Installing System Analytic Package...');

        if (!$this->configExists($this->configFileName)) {
            $this->info('Publishing configuration...');
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }
    }

    private function configExists($fileName)
    {
        return file_exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Kakaprodo\SystemAnalytic\SystemAnalyticServiceProvider",
            '--tag' => "config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
