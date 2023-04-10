<?php

namespace App\Utilities\Analytics\Lib\ExportHub\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExampleExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    /**
     * @var Invoice $invoice
     */
    public function map($order): array
    {
        return [
            $order->order_number,
            $order->total_price,

        ];
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Cost Brut(RWF)',
        ];
    }
}
