<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWorkType;
use App\Models\WorkCategory;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeWorkTypeController extends Controller
{
    /**
     * Show the form for editing the specified employee's work type selections.
     */
    public function edit(Employee $employee)
    {
        // Get all work categories with their work types
        $categories = WorkCategory::with('workTypes')->orderBy('name')->get();

        // Get the employee's current work type selections
        $selections = EmployeeWorkType::where('employee_id', $employee->id)
            ->get()
            ->keyBy('work_category_id');

        return Inertia::render('Employees/WorkTypes', [
            'employee' => $employee,
            'categories' => $categories,
            'selections' => $selections,
        ]);
    }

    /**
     * Update the specified employee's work type selections.
     */
    public function update(Request $request, Employee $employee)
    {
        // Validate the request
        $validated = $request->validate([
            'selections' => 'required|array',
            'selections.*.work_category_id' => 'required|exists:work_categories,id',
            'selections.*.work_type_id' => 'nullable|exists:work_types,id',
        ]);

        // Process each selection
        foreach ($validated['selections'] as $selection) {
            $categoryId = $selection['work_category_id'];
            $typeId = $selection['work_type_id'] ?? null;

            // Update or create the selection
            EmployeeWorkType::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'work_category_id' => $categoryId,
                ],
                [
                    'work_type_id' => $typeId,
                ]
            );
        }

        return redirect()->route('employees.work-types.edit', $employee->id)
            ->with('success', 'Work type selections saved successfully.');
    }
}
