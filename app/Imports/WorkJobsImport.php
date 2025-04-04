<?php

namespace App\Imports;

use App\Models\WorkJob;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class WorkJobsImport implements ToModel, WithStartRow, WithChunkReading
{
    use Importable;

    private $rowCount = 0;
    private $successCount = 0;
    private $skipCount = 0;
    private $errorCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowCount++;

        // Log the row data for debugging
        Log::info("Processing row {$this->rowCount}", [
            'B' => $row[1] ?? 'NULL',
            'C' => $row[2] ?? 'NULL',
            'H' => $row[7] ?? 'NULL',
            'I' => $row[8] ?? 'NULL',
        ]);

        // Skip if code or description is empty
        if (empty($row[1]) || empty($row[2])) {
            $this->skipCount++;
            Log::warning("Skipping row {$this->rowCount} due to missing code or description");
            return null;
        }

        // Check if client fields are empty and provide defaults
        $clientName = !empty($row[7]) ? $row[7] : 'Unknown Client';
        $clientId = !empty($row[8]) ? $row[8] : 'N/A';

        if (empty($row[7]) || empty($row[8])) {
            Log::info("Row {$this->rowCount}: Using default client information for job code {$row[1]}");
        }

        try {
            $job = WorkJob::updateOrCreate(
                [
                    'code' => $row[1],
                ],
                [
                    'description' => $row[2],
                    'client_name' => $clientName,
                    'client_id' => $clientId,
                    'is_active' => true,
                ]
            );

            $this->successCount++;
            Log::info("Successfully imported job: {$row[1]}");

            return $job;
        } catch (\Exception $e) {
            $this->errorCount++;
            Log::error("Error importing row {$this->rowCount}: " . $e->getMessage(), [
                'exception' => $e,
                'row' => $row,
            ]);

            return null;
        }
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 3; // Skip the two header rows
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get import statistics
     */
    public function getStats(): array
    {
        return [
            'total' => $this->rowCount,
            'success' => $this->successCount,
            'skipped' => $this->skipCount,
            'errors' => $this->errorCount,
        ];
    }
}
