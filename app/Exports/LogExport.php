<?php

// app/Exports/LogExport.php

namespace App\Exports;

use App\Models\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;




class LogExport implements FromCollection
{
    public function headings(): array
    {
        return [
            'ID',
            'type',
            'Message',
            'Context',
            'Extra',
            'created_at',
            'updated_at',
        ];
    }

    public function collection()
    {
        $logs = Log::all();

        $logs->prepend($this->headings());

        return $logs;
    }
    
}
