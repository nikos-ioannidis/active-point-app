<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportWorkEntry;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\WorkCategory;
use App\Models\WorkJob;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // For admin, show all reports
        if ($user && $user->roles && $user->roles->contains('name', 'Admin')) {
            $reports = DailyReport::with(['employee', 'vehicle', 'workJob'])
                ->orderBy('report_date', 'desc')
                ->paginate(10);
        } else {
            // For technician, show only their reports
            $reports = DailyReport::with(['vehicle', 'workJob'])
                ->where('employee_id', $employee->id)
                ->orderBy('report_date', 'desc')
                ->paginate(10);
        }

        // Ensure we have a properly structured response even if there are no reports
        return Inertia::render('DailyReports/Index', [
            'reports' => [
                'data' => $reports->items(),
                'links' => $reports->toArray()['links'] ?? [],
                'meta' => [
                    'current_page' => $reports->currentPage(),
                    'from' => $reports->firstItem() ?? 0,
                    'last_page' => $reports->lastPage(),
                    'links' => $reports->linkCollection()->toArray(),
                    'path' => $reports->path(),
                    'per_page' => $reports->perPage(),
                    'to' => $reports->lastItem() ?? 0,
                    'total' => $reports->total(),
                ],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $isAdmin = $user && $user->roles && $user->roles->contains('name', 'Admin');

        // Get all active vehicles
        $vehicles = Vehicle::where('is_active', true)
            ->orderBy('license_plate')
            ->get();

        // Get work categories
        $workTypes = WorkCategory::orderBy('name')->get();

        // Get active jobs
        $workJobs = WorkJob::where('is_active', true)
            ->orderBy('code')
            ->get();

        // Get all employees for admin users
        $employees = $isAdmin ? Employee::isActive()->orderBy('employee_name')->get() : null;

        return Inertia::render('DailyReports/Form', [
            'employee' => $employee,
            'vehicles' => $vehicles,
            'workTypes' => $workTypes,
            'workJobs' => $workJobs,
            'mode' => 'create',
            'isAdmin' => $isAdmin,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $isAdmin = $user && $user->roles && $user->roles->contains('name', 'Admin');

        // Prepare validation rules
        $validationRules = [
            'report_date' => [
                'required',
                'date',
            ],
            'work_job_id' => 'required|exists:work_jobs,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'notes' => 'nullable|string',
            'work_entries' => 'required|array|min:1',
            'work_entries.*.work_type_id' => 'required|exists:work_categories,id',
            'work_entries.*.start_time' => 'required|date_format:H:i',
            'work_entries.*.end_time' => 'required|date_format:H:i|after:work_entries.*.start_time',
            'work_entries.*.description' => 'nullable|string',
        ];

        // Add employee_id validation for admins
        if ($isAdmin) {
            $validationRules['employee_id'] = 'required|exists:employees,id';
        } else {
            // For non-admins, ensure report date is unique for the employee
            $validationRules['report_date'][] = Rule::unique('daily_reports')->where(function ($query) use ($employee) {
                return $query->where('employee_id', $employee->id);
            });
        }

        // Validate the request
        $validated = $request->validate($validationRules);

        // Determine employee_id to use
        $employeeId = $isAdmin && isset($validated['employee_id'])
            ? $validated['employee_id']
            : $employee->id;

        // Create the daily report
        $report = DailyReport::create([
            'employee_id' => $employeeId,
            'report_date' => $validated['report_date'],
            'work_job_id' => $validated['work_job_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'notes' => $validated['notes'],
            'total_minutes' => 0, // Will be updated after work entries are created
        ]);

        // Create the work entries
        foreach ($validated['work_entries'] as $entry) {
            // Find a work type from the selected category
            $workType = WorkType::where('work_category_id', $entry['work_type_id'])->first();

            if ($workType) {
                DailyReportWorkEntry::create([
                    'daily_report_id' => $report->id,
                    'work_type_id' => $workType->id,
                    'start_time' => $entry['start_time'],
                    'end_time' => $entry['end_time'],
                    'description' => $entry['description'] ?? null,
                ]);
            }
        }

        // Calculate and update total hours
        $this->updateTotalHours($report);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load(['employee', 'vehicle', 'workJob', 'workEntries.workType']);

        return Inertia::render('DailyReports/Show', [
            'report' => $dailyReport,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyReport $dailyReport)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $isAdmin = $user && $user->roles && $user->roles->contains('name', 'Admin');

        // Check if the user is authorized to edit this report
        if (!$isAdmin && $dailyReport->employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.');
        }

        $dailyReport->load(['workEntries', 'workJob']);

        // Get all active vehicles
        $vehicles = Vehicle::orderBy('license_plate')
            ->get();

        // Get work categories
        $workTypes = WorkCategory::orderBy('name')->get();
        $reportEmployee = $dailyReport->employee;

        // Get active jobs
        $workJobs = WorkJob::orderBy('code')
            ->get();

        // Get all employees for admin users
        $employees = $isAdmin ? Employee::orderBy('employee_name')->get() : null;

        // Format work entries for the form
        $workEntries = $dailyReport->workEntries->map(function ($entry) {
            // Get the work category ID from the work type
            $workCategoryId = $entry->workType ? $entry->workType->work_category_id : null;

            return [
                'id' => $entry->id,
                'work_type_id' => $workCategoryId,
                'start_time' => date('H:i', strtotime($entry->start_time)),
                'end_time' => date('H:i', strtotime($entry->end_time)),
                'description' => $entry->description,
            ];
        });

        // Format the report date for the date input (YYYY-MM-DD)
        $formattedReportDate = date('Y-m-d', strtotime($dailyReport->report_date));

        return Inertia::render('DailyReports/Form', [
            'report' => array_merge($dailyReport->toArray(), [
                'work_entries' => $workEntries,
                'report_date' => $formattedReportDate,
            ]),
            'employee' => $reportEmployee,
            'vehicles' => $vehicles,
            'workTypes' => $workTypes,
            'workJobs' => $workJobs,
            'mode' => 'edit',
            'isAdmin' => $isAdmin,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyReport $dailyReport)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $isAdmin = $user && $user->roles && $user->roles->contains('name', 'Admin');

        // Check if the user is authorized to update this report
        if (!$isAdmin && $dailyReport->employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.');
        }

        // Prepare validation rules
        $validationRules = [
            'report_date' => [
                'required',
                'date',
            ],
            'work_job_id' => 'required|exists:work_jobs,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'notes' => 'nullable|string',
            'work_entries' => 'required|array|min:1',
            'work_entries.*.id' => 'nullable|exists:daily_report_work_entries,id',
            'work_entries.*.work_type_id' => 'required|exists:work_categories,id',
            'work_entries.*.start_time' => 'required|date_format:H:i',
            'work_entries.*.end_time' => 'required|date_format:H:i|after:work_entries.*.start_time',
            'work_entries.*.description' => 'nullable|string',
        ];

        // For non-admins, ensure report date is unique for the employee
        if (!$isAdmin) {
            $validationRules['report_date'][] = Rule::unique('daily_reports')->where(function ($query) use ($dailyReport, $employee) {
                return $query->where('employee_id', $employee->id)
                    ->where('id', '!=', $dailyReport->id);
            });
        }

        // Validate the request
        $validated = $request->validate($validationRules);

        // Update the daily report
        $dailyReport->update([
            'report_date' => $validated['report_date'],
            'work_job_id' => $validated['work_job_id'],
            'vehicle_id' => $validated['vehicle_id'],
            'notes' => $validated['notes'],
        ]);

        // Get existing work entries
        $existingEntries = $dailyReport->workEntries->keyBy('id');
        $updatedEntryIds = [];

        // Update or create work entries
        foreach ($validated['work_entries'] as $entry) {
            // Find a work type from the selected category
            $workType = WorkType::where('work_category_id', $entry['work_type_id'])->first();

            if ($workType) {
                if (isset($entry['id']) && $existingEntries->has($entry['id'])) {
                    // Update existing entry
                    $existingEntries[$entry['id']]->update([
                        'work_type_id' => $workType->id,
                        'start_time' => $entry['start_time'],
                        'end_time' => $entry['end_time'],
                        'description' => $entry['description'] ?? null,
                    ]);
                    $updatedEntryIds[] = $entry['id'];
                } else {
                    // Create new entry
                    $newEntry = DailyReportWorkEntry::create([
                        'daily_report_id' => $dailyReport->id,
                        'work_type_id' => $workType->id,
                        'start_time' => $entry['start_time'],
                        'end_time' => $entry['end_time'],
                        'description' => $entry['description'] ?? null,
                    ]);
                    $updatedEntryIds[] = $newEntry->id;
                }
            }
        }

        // Delete entries that were not updated
        foreach ($existingEntries as $entry) {
            if (!in_array($entry->id, $updatedEntryIds)) {
                $entry->delete();
            }
        }

        // Calculate and update total hours
        $this->updateTotalHours($dailyReport);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report updated successfully.');
    }

    /**
     * Calculate and update the total minutes for a daily report
     */
    private function updateTotalHours(DailyReport $dailyReport): void
    {
        // Reload work entries to ensure we have the latest data
        $dailyReport->load('workEntries');

        $totalMinutes = 0;

        foreach ($dailyReport->workEntries as $entry) {
            // Parse start and end times
            $startTime = $dailyReport->report_date->setTimeFromTimeString($entry->start_time);
            $endTime = $dailyReport->report_date->setTimeFromTimeString($entry->end_time);

            // If end time is before start time, assume it's the next day
            if ($endTime->lt($startTime)) {
                $endTime->addDay();
            }

            // Calculate duration in minutes
            $durationMinutes = $startTime->diffInMinutes($endTime);
            $totalMinutes += $durationMinutes;
        }

        // Update the daily report with total minutes
        $dailyReport->update(['total_minutes' => $totalMinutes]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyReport $dailyReport)
    {
        $user = Auth::user();

        // Only admins can delete reports
        if (!($user && $user->roles && $user->roles->contains('name', 'Admin'))) {
            abort(403, 'Unauthorized action.');
        }

        $dailyReport->delete();

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report deleted successfully.');
    }
}
