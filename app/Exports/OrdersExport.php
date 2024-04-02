<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::select('id', 'first_name', 'last_name', 'email','address', 'grand_total')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Address',
            'Total',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply bold style to header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(14);
    }
}
