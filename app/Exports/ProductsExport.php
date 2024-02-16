<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ProductsExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::select('id', 'title', 'price', 'sku', 'qty')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Price',
            'SKU',
            'Qty',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply bold style to header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(14);
    }
}
