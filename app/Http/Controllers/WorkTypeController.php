<?php

namespace App\Http\Controllers;

use App\Models\WorkCategory;
use App\Models\WorkType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkTypeController extends Controller
{
    /**
     * Display a listing of the work types.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');

        $workTypes = WorkType::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('work_category_id', $categoryId);
            })
            ->with('workCategory')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = WorkCategory::orderBy('name')->get();

        return Inertia::render('WorkTypes/Index', [
            'workTypes' => $workTypes,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $categoryId,
            ],
        ]);
    }

    /**
     * Show the form for creating a new work type.
     */
    public function create()
    {
        $categories = WorkCategory::orderBy('name')->get();

        return Inertia::render('WorkTypes/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created work type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_category_id' => 'required|exists:work_categories,id',
            'name' => 'required|string|max:255',
            'price_standard' => 'required|numeric|min:0',
            'price_gamesa' => 'nullable|numeric|min:0',
            'price_gamesa_abroad' => 'nullable|numeric|min:0',
            'max_hours' => 'required|integer|min:0',
        ]);

        WorkType::create($validated);

        return redirect()->route('work-types.index')
            ->with('success', 'Work type created successfully.');
    }

    /**
     * Display the specified work type.
     */
    public function show(WorkType $workType)
    {
        return Inertia::render('WorkTypes/Show', [
            'workType' => $workType->load('workCategory'),
        ]);
    }

    /**
     * Show the form for editing the specified work type.
     */
    public function edit(WorkType $workType)
    {
        $categories = WorkCategory::orderBy('name')->get();

        return Inertia::render('WorkTypes/Edit', [
            'workType' => $workType,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified work type in storage.
     */
    public function update(Request $request, WorkType $workType)
    {
        $validated = $request->validate([
            'work_category_id' => 'required|exists:work_categories,id',
            'name' => 'required|string|max:255',
            'price_standard' => 'required|numeric|min:0',
            'price_gamesa' => 'nullable|numeric|min:0',
            'price_gamesa_abroad' => 'nullable|numeric|min:0',
            'max_hours' => 'required|integer|min:0',
        ]);

        $workType->update($validated);

        return redirect()->route('work-types.index')
            ->with('success', 'Work type updated successfully.');
    }

    /**
     * Remove the specified work type from storage.
     */
    public function destroy(WorkType $workType)
    {
        $workType->delete();

        return redirect()->route('work-types.index')
            ->with('success', 'Work type deleted successfully.');
    }
}
