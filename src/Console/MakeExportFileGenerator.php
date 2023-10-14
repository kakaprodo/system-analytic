<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Support\Arr;
use Illuminate\Console\GeneratorCommand;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class MakeExportFileGenerator extends GeneratorCommand
{
    protected $hidden = true;

    /**
     * type can be: BarChart, CardCoun, List, PieChart
     */
    protected $signature = 'system-analytic:export {name}';

    protected $description = 'Create an export file for a list analytic type';

    protected function getStub()
    {
        return __DIR__ . "/Stubs/Exports/Export.php.stub";
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . Util::folderFromPath(config('system-analytic.analytic_path')) . '\\' . config('system-analytic.folder_name') . '\Exports';
    }

    public function handle()
    {
        parent::handle();

        $this->reformatHandlerClassContent();
    }

    protected function reformatHandlerClassContent()
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
