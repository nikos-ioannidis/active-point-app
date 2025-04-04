<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeIrataLevelEnum;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $employees = Employee::query()
            ->when($search, function ($query, $search) {
                $query->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%");
            })
            ->with('user')
            ->orderBy('employee_name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        // Get users who are not already associated with an employee
        $usersWithoutEmployee = User::whereDoesntHave('employee')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });

        return Inertia::render('Employees/Create', [
            'users' => $usersWithoutEmployee,
            'irataLevels' => $this->getIrataLevelOptions(),
        ]);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $existingEmployee = Employee::where('user_id', $value)->first();
                        if ($existingEmployee) {
                            $fail('This user is already associated with another employee.');
                        }
                    }
                },
            ],
            'employee_code' => 'required|string|max:255|unique:employees',
            'employee_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'is_active' => 'boolean',
            'owns_equipment' => 'boolean',
            'is_contractor' => 'boolean',
            'irata_level' => 'required|in:' . implode(',', EmployeeIrataLevelEnum::values()),
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        return Inertia::render('Employees/Show', [
            'employee' => $employee->load('user'),
            'irataLevels' => $this->getIrataLevelOptions(),
        ]);
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        // Get users who are not already associated with an employee or are associated with this employee
        $availableUsers = User::where(function ($query) use ($employee) {
                $query->whereDoesntHave('employee')
                    ->orWhereHas('employee', function ($query) use ($employee) {
                        $query->where('id', $employee->id);
                    });
            })
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });

        return Inertia::render('Employees/Edit', [
            'employee' => $employee->load('user'),
            'users' => $availableUsers,
            'irataLevels' => $this->getIrataLevelOptions(),
        ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'user_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($employee) {
                    if ($value) {
                        $existingEmployee = Employee::where('user_id', $value)
                            ->where('id', '!=', $employee->id)
                            ->first();
                        if ($existingEmployee) {
                            $fail('This user is already associated with another employee.');
                        }
                    }
                },
            ],
            'employee_code' => 'required|string|max:255|unique:employees,employee_code,' . $employee->id,
            'employee_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'is_active' => 'boolean',
            'owns_equipment' => 'boolean',
            'is_contractor' => 'boolean',
            'irata_level' => 'required|in:' . implode(',', EmployeeIrataLevelEnum::values()),
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Archive the specified employee (mark as inactive).
     */
    public function destroy(Employee $employee)
    {
        $employee->update(['is_active' => false]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee archived successfully.');
    }

    /**
     * Get IRATA level options with value and label.
     *
     * @return array<array<string, string>>
     */
    private function getIrataLevelOptions(): array
    {
        $options = [];
        foreach (EmployeeIrataLevelEnum::cases() as $case) {
            $options[] = [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }
        return $options;
    }
}
