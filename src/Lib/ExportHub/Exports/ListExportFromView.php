<?php

namespace Kakaprodo\SystemAnalytic\Lib\ExportHub\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class ListExportFromView implements FromView
{
    use Exportable;

    protected $query;

    protected $viewTemplate;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Set the view template of the export
     */
    public function viewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }

    public function view(): View
    {
        return view($this->viewTemplate, [
            'orders' => $this->query->get()
        ]);
    }
}
