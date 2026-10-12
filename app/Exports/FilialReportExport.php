<?php


namespace App\Exports;

use App\Models\Filial;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class FilialReportExport implements FromCollection, WithHeadings
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function headings(): array
    {
        return [
            'Филиал',
            'Кол-во тестирований',
        ];
    }

    public function collection()
    {
        $filials = Filial::withCount(['users' => function ($query) {
            $query->whereYear('created_at', $this->year);
        }])->get();

        $exportData = $filials->map(function ($filial) {
            return [
                'name' => $filial->name,
                'users_count' => $filial->users_count,
            ];
        });

        return $exportData;
        
    }
}