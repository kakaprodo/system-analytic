<?php

namespace Kakaprodo\SystemAnalytic\Console;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Console\GeneratorCommand;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class AnalyticHandlerGenerator extends GeneratorCommand
{
    protected $hidden = true;

    /**
     * type can be: BarChart, CardCoun, List, PieChart
     */
    protected $signature = 'system-analytic:handler {name} {--type=} {--bar-chart} {--list} {--card-count} {--computed} {--pie-chart}';

    protected $description = 'Create the System Analytic Skeleton';

    /**
     * the folder in which all the analytic skeleton are stored
     */
    protected $hubLocation = null;

    protected static $expectedOptions = [
        'bar-chart' => 'BarChart',
        'list' => 'List',
        'card-count' => 'CardCount',
        'computed' => 'Computed',
        'pie-chart' => 'PieChart',
    ];

    protected function getStub()
    {
        return __DIR__ . "/Stubs/{$this->detectChartType()}.php.stub";
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . Util::folderFromPath(config('system-analytic.analytic_path')) . '\\' . config('system-analytic.folder_name') . '\Handlers';
    }

    /**
     * From the provided options, 
     */
    private function detectChartType()
    {
        $typeOption = $this->option('type');
        $chartType = null;

        if ($typeOption) {
            $indexExpectedChartType = array_search($typeOption, array_values(static::$expectedOptions));

            if ($indexExpectedChartType === false) {
                $this->error(
                    "the type value must be one of:" . implode(',', array_values(static::$expectedOptions))
                );
                exit();
            }

            $chartType = $typeOption;
        } else {
            $inputedOptions = Arr::only($this->options(), array_keys(static::$expectedOptions));


            foreach ($inputedOptions as $inputOption => $wasSelected) {
                if (!$wasSelected) continue;

                $chartType = static::$expectedOptions[$inputOption];

                break;
            }
        }


        if (!$chartType) {
            $this->error(
                "the chart type argument is missing, please add to your command one"
                    . " of: --bar-chart,--list,--card-count,--computed, --pie-chart  "
            );
            exit();
        }

        return $chartType . 'Handler';
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
