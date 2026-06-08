<?php

namespace App\Imports;

use App\Models\Household;
use App\Models\Resident;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

/**
 * Import residents from Excel file.
 * Expected columns (case-insensitive heading row):
 *   first_name, last_name, middle_name, birth_date, gender,
 *   civil_status, contact_number, address, purok, barangay
 */
class ResidentsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public int $importedCount = 0;

    public function model(array $row): ?Resident
    {
        // Skip empty rows
        if (empty($row['first_name']) || empty($row['last_name'])) {
            return null;
        }

        // Parse birth date flexibly
        $birthDate = null;
        if (!empty($row['birth_date'])) {
            try {
                $birthDate = Carbon::parse($row['birth_date'])->format('Y-m-d');
            } catch (\Throwable $e) {
                $birthDate = null;
            }
        }

        // Find or create household
        $household = null;
        if (!empty($row['address'])) {
            $household = Household::firstOrCreate(
                ['address' => trim($row['address'])],
                [
                    'barangay' => $row['barangay'] ?? 'N/A',
                    'purok'    => $row['purok']    ?? null,
                ]
            );
        }

        $this->importedCount++;

        return new Resident([
            'first_name'      => trim($row['first_name']),
            'middle_name'     => $row['middle_name'] ?? null,
            'last_name'       => trim($row['last_name']),
            'birth_date'      => $birthDate,
            'gender'          => strtolower($row['gender'] ?? 'male'),
            'civil_status'    => strtolower($row['civil_status'] ?? 'single'),
            'contact_number'  => $row['contact_number'] ?? 'N/A',
            'address'         => $row['address'] ?? 'N/A',
            'household_id'    => $household?->id,
            'source'          => 'census',
        ]);
    }
}
