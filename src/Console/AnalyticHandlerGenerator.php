<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class AnalyticHandlerGenerator extends GeneratorCommand
{
    protected $hidden = true;

    /**
     * type can be: BarChart, CardCoun, List, PieChart
     */
    protected $signature = 'system-analytic:handler {name} {type=BarChart}';

    protected $description = 'Create the System Analytic Skeleton';

    /**
     * the folder in which all the analytic skeleton are stored
     */
    protected $hubLocation = null;

    protected function getStub()
    {
        return __DIR__ . "/Stubs/{$this->argument('type')}Handler.php.stub";
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . Util::folderFromPath(config('system-analytic.analytic_path')) . '\\' . config('system-analytic.folder_name') . '\Handlers';
    }

    public function handle()
    {
        parent::handle();

        $this->writeStubToCreatedFile();
    }

    protected function writeStubToCreatedFile()
    {
        // Get the fully qualified class name (FQN)
        $class = $this->qualifyClass($this->getNameInput());

        $classNameSpace = $this->getNamespace($class);

        // get the destination path, based on the default namespace
        $classPath = $this->getPath($class);

        $content = file_get_contents($classPath);

        $formattedContent = strtr($content, [
            '{name_space}' => $classNameSpace,
            '{class_name}' => collect(explode('\\', $class))->last()
        ]);

        // // Update the file content with additional data (regular expressions)

        file_put_contents($classPath, $formattedContent);
    }
}
