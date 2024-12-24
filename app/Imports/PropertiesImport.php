<?php

namespace App\Imports;

use App\Models\Property;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PropertiesImport implements ToModel, WithChunkReading, WithHeadingRow
{
    /**
     * Define the mapping from the CSV file to the Property Model
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Property([
            'uprn' => $row['uprn'],
            'house_number' => $row['housenumber'],
            'house_name' => $row['housename'],
            'street' => $row['street'],
            'town' => $row['town'],
            'parish' => $row['parish'],
            'county' => $row['county'],
            'postcode' => $row['postcode'],
        ]);
    }

    public function chunkSize(): int {
        return 100;
    }

    public function headingRow(): int{
        return 1;
    }

}
