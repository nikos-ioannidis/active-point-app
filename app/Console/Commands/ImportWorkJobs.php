<?php

namespace App\Console\Commands;

use App\Imports\WorkJobsImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportWorkJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:work-jobs {file?} {--debug : Enable detailed logging}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import work jobs from Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file') ?? 'storage/mock-files/export.xlsx';
        $debug = $this->option('debug');

        if ($debug) {
            $this->info('Debug mode enabled - detailed logs will be written to laravel.log');
        }

        $this->info('Importing work jobs from Excel file...');
        Log::info('Starting import of work jobs from: ' . $file);

        try {
            // Use the import object directly to get statistics
            $import = new WorkJobsImport();
            $import->import($file);

            // Get and display statistics
            $stats = $import->getStats();

            $this->info('Import completed!');
            $this->table(
                ['Total Rows', 'Successful', 'Skipped', 'Errors'],
                [[$stats['total'], $stats['success'], $stats['skipped'], $stats['errors']]]
            );

            // Log the statistics
            Log::info('Import completed with statistics', $stats);

            if ($stats['errors'] > 0) {
                $this->warn('Some rows had errors. Check the log file for details.');
            }

            if ($stats['skipped'] > 0) {
                $this->warn('Some rows were skipped due to missing required fields. Check the log file for details.');
            }
        } catch (\Exception $e) {
            Log::error('Error during import: ' . $e->getMessage(), ['exception' => $e]);
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
