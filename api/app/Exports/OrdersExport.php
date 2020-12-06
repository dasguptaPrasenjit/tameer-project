<?php

namespace App\Exports;

use App\Http\Entities\OrderMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromArray, WithMapping, WithHeadings, WithColumnFormatting, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    /* public function collection()
    {
        return OrderMaster::all();
    } */
    protected $orders;

    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function array(): array
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Transaction#',
            'Address',
            'Order amount',
            'Delivery',
            'Tax',
            'Total',
            'Payment type',
            'Transaction status',
            'Created at',
            'Updated at'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]]
        ];
    }

    public function map($orders): array
    {
        return [
            $orders->transaction_id,
            $orders->delivery_address,
            $orders->order_amount,
            $orders->delivery_amount,
            $orders->tax_amount,
            $orders->transaction_amount,
            $orders->payment_type,
            $orders->transaction_status,
            $orders->created_at,
            $orders->updated_at
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_00,
            'D' => NumberFormat::FORMAT_NUMBER_00,
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00
        ];
    }
    
}
