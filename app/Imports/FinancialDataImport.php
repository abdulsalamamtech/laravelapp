<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

// class FinancialDataImport implements ToCollection
// {
//     /**
//      * @param Collection $collection
//      */
//     public function collection(Collection $collection) {}
// }

/**
 * Import class for Laravel Excel
 */
class FinancialDataImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // This is handled by the service
        // Laravel Excel will call this for each sheet
        return $rows;
    }
}
