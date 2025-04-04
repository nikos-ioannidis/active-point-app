<?php

namespace App\Http\Controllers;

use App\Imports\WorkJobsImport;
use App\Models\WorkJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class WorkJobController extends Controller
{
    /**
     * Display a listing of the jobs.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $client = $request->input('client');

        $jobs = WorkJob::query()
            ->when($search, function ($query, $search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->when($client, function ($query, $client) {
                $query->where('client_name', 'like', "%{$client}%")
                    ->orWhere('client_id', 'like', "%{$client}%");
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('WorkJobs/Index', [
            'workJobs' => $jobs,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'client' => $client,
            ],
        ]);
    }

    /**
     * Show the form for creating a new job.
     */
    public function create()
    {
        return Inertia::render('WorkJobs/Create');
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:work_jobs',
            'description' => 'required|string',
            'client_name' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        WorkJob::create($validated);

        return redirect()->route('work-jobs.index')
            ->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified job.
     */
    public function show(WorkJob $workJob)
    {
        return Inertia::render('WorkJobs/Show', [
            'workJob' => $workJob,
        ]);
    }

    /**
     * Show the form for editing the specified job.
     */
    public function edit(WorkJob $workJob)
    {
        return Inertia::render('WorkJobs/Edit', [
            'workJob' => $workJob,
        ]);
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, WorkJob $workJob)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:work_jobs,code,' . $workJob->id,
            'description' => 'required|string',
            'client_name' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $workJob->update($validated);

        return redirect()->route('work-jobs.index')
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Import jobs from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            // Use the import object directly to get statistics
            $import = new WorkJobsImport();
            $import->import($request->file('file'));

            // Get statistics
            $stats = $import->getStats();

            // Log the statistics
            Log::info('Import completed with statistics', $stats);

            $message = "Import completed: {$stats['success']} jobs imported successfully";

            if ($stats['skipped'] > 0) {
                $message .= ", {$stats['skipped']} rows skipped";
            }

            if ($stats['errors'] > 0) {
                $message .= ", {$stats['errors']} errors encountered";
            }

            return redirect()->route('work-jobs.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error during import: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('work-jobs.index')
                ->with('error', 'Error importing jobs: ' . $e->getMessage());
        }
    }
}
