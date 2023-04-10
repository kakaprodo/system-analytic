<?php

namespace Kakaprodo\SystemAnalytic\Lib\ExportHub;

use Exception;
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

    public function toExcelFromView()
    {
        return (new ListExportFromView(
            $this->response->result
        ))->viewTemplate($this->viewTemplate)
            ->download($this->fileName);
    }
}
