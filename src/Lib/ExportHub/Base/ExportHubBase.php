<?php

namespace Kakaprodo\SystemAnalytic\Lib\ExportHub\Base;

use Exception;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;


abstract class ExportHubBase
{
    protected $response;

    /**
     * The extensiion of the file to be exported
     */
    protected $fileExtension;

    /**
     * The name of the file to be exported
     */
    protected $fileName;

    protected $viewTemplate; // view name

    protected $exportType; // excel.csv and view template

    const EXCEL = 'xlsx';
    const CSV = 'csv';
    const EXCEL_FRROM_VIEW = 'view';

    public static $supporteFiles = [
        self::EXCEL,
        self::CSV,
        self::EXCEL_FRROM_VIEW
    ];

    public function __construct(AnalyticResponse $response)
    {
        if (!class_exists('Maatwebsite\Excel\Excel')) {
            throw new Exception('The system analytic package requires the latest version of maatwebsite/excel, please install this package first');
        }

        $this->response = $response;
        $fileName = className($this->response->handler);
        $this->fileExtension = $this->response->handler->exportFile;
        $this->fileName = strTitle($fileName) . '.' . $this->fileExtension;
        $this->viewTemplate = $this->response->handler->exportView;

        $this->exportType = $this->viewTemplate
            ? self::EXCEL_FRROM_VIEW
            : $this->response->handler->exportFile;
    }

    /**
     * create the instance
     */
    public static function make(AnalyticResponse $response)
    {
        return (new static($response))
            ->validate()
            ->detectExportType();
    }

    protected function validate()
    {
        return $this;
    }

    protected function detectExportType()
    {
        $exportHandler =  [
            self::EXCEL => fn () => $this->toExcel(),
            self::CSV => fn () =>  $this->toExcel(),
            self::EXCEL_FRROM_VIEW => fn () =>  $this->toExcelFromView(),
        ][$this->exportType] ?? null;

        return callFunction(
            $exportHandler,
            "The export type: {$this->exportType} is not supported"
        );
    }
}
