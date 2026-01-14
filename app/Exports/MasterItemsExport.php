<?php

namespace App\Exports;

use App\Models\MasterItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = MasterItem::query();

        // Apply filters
        if (!empty($this->filters['kode'])) {
            $query->where('kode', $this->filters['kode']);
        }
        if (!empty($this->filters['nama'])) {
            $query->where('nama', 'LIKE', '%' . $this->filters['nama'] . '%');
        }
        if (!empty($this->filters['kategori'])) {
            $query->whereHas('kategoris', function($q) {
                $q->where('kategoris.id', $this->filters['kategori']);
            });
        }
        if (!empty($this->filters['hargamin'])) {
            $query->where('harga_beli', '>=', $this->filters['hargamin']);
        }
        if (!empty($this->filters['hargamax'])) {
            $query->where('harga_beli', '<=', $this->filters['hargamax']);
        }

        return $query->with('kategoris')->orderBy('id')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama items',
            'Kategori',
            'Nama supplier',
            'Harga',
            'Laba',
            'Harga jual'
        ];
    }

    /**
     * @param mixed $item
     * @return array
     */
    public function map($item): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Get category names separated by comma
        $kategoriNames = $item->kategoris->pluck('nama')->implode(', ');

        // Calculate selling price
        $hargaJual = $item->harga_beli + ($item->harga_beli * $item->laba / 100);

        return [
            $rowNumber,
            $item->nama,
            $kategoriNames ?: '-',
            $item->supplier,
            $item->harga_beli,
            $item->laba . '%',
            $hargaJual
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
