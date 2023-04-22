<?php

namespace Kakaprodo\SystemAnalytic\Lib\ExportHub;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\ExportHub\Base\ExportHubBase;
use Kakaprodo\SystemAnalytic\Lib\ExportHub\Exports\ListExportFromView;

class ExportHub extends ExportHubBase
{
    public function toExcel()
    {
        $exportFormatterClass = $this->response->handler->exportClass;

        if (!$exportFormatterClass) throw new Exception('Missing the $exportClass value on the analytic handler');

        return (new $exportFormatterClass(
            $this->response->result
        ))->download($this->fileName);
    }

    /**
     * Writte to file, then dowload the file
     */
    public function toCsv()
    {
        $exportFormatterClass = $this->response->handler->exportClass;

        if (!$exportFormatterClass) throw new Exception('Missing the $exportClass value on the analytic handler');

        $exportFormatter = new $exportFormatterClass();

        $query = $this->response->result;
        $fileName =  (now()->toDateString()) . '-' . Util::uuid() . '-' . (Str::random(5)) . '-' . $this->fileName;
        $directory = 'public/systemAnalyticExports/';
        $filePath =  $directory  . $fileName;
        Storage::put($filePath, '');

        $fileToExport = fopen(storage_path('app/' . $filePath), 'w');

        // set headings
        fputcsv($fileToExport, $exportFormatter->headings());

        $query->chunk(
            config('system-analytic.chunk_number_for_export', 1000),
            function ($items) use ($fileToExport, $exportFormatter) {
                foreach ($items as  $item) {
                    fputcsv($fileToExport, $exportFormatter->map($item));
                }
            }
        );

        fclose($fileToExport);

        return Storage::download($filePath);
    }

    public function toExcelFromView()
    {
        return (new ListExportFromView(
            $this->response->result
        ))->viewTemplate($this->viewTemplate)
            ->download($this->fileName);
    }
}
