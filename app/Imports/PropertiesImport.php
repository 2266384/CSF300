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

        //dd($row);

        // Check if we have a street and skip the property import if we don't
        if (empty($row['thoroughfare'])) {
            return null;
        }

        return new Property([
            'uprn' => $row['udprn'],
            'house_number' => $row['buildingnumber'],
            'house_name' => empty($row['buildingname']) && empty($row['subbuildingname'])
                ? ''
                : (empty($row['buildingname'])
                    ? $row['subbuildingname']
                    : (empty($row['subbuildingname'])
                        ? $row['buildingname']
                        : implode(', ', [$row['buildingname'], $row['subbuildingname']])
                    )
                ),
            'street' => $row['thoroughfare'],
            'town' => $row['doubledependentlocality'],
            'parish' => $row['dependentlocality'],
            'county' => $row['posttown'],
            'postcode' => $row['postcode'],
        ]);

    }

    public function chunkSize(): int {
        return 10000;
    }

    public function headingRow(): int{
        return 1;
    }

}
