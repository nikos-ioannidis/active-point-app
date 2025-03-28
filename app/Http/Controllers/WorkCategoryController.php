<?php

namespace App\Http\Controllers;

use App\Models\WorkCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkCategoryController extends Controller
{
    /**
     * Display a listing of the work categories.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $workCategories = WorkCategory::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('workTypes')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('WorkCategories/Index', [
            'workCategories' => $workCategories,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new work category.
     */
    public function create()
    {
        return Inertia::render('WorkCategories/Create');
    }

    /**
     * Store a newly created work category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:work_categories',
        ]);

        WorkCategory::create($validated);

        return redirect()->route('work-categories.index')
            ->with('success', 'Work category created successfully.');
    }

    /**
     * Display the specified work category.
     */
    public function show(WorkCategory $workCategory)
    {
        return Inertia::render('WorkCategories/Show', [
            'workCategory' => $workCategory->load('workTypes'),
        ]);
    }

    /**
     * Show the form for editing the specified work category.
     */
    public function edit(WorkCategory $workCategory)
    {
        return Inertia::render('WorkCategories/Edit', [
            'workCategory' => $workCategory,
        ]);
    }

    /**
     * Update the specified work category in storage.
     */
    public function update(Request $request, WorkCategory $workCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:work_categories,name,' . $workCategory->id,
        ]);

        $workCategory->update($validated);

        return redirect()->route('work-categories.index')
            ->with('success', 'Work category updated successfully.');
    }

    /**
     * Remove the specified work category from storage.
     */
    public function destroy(WorkCategory $workCategory)
    {
        $workCategory->delete();

        return redirect()->route('work-categories.index')
            ->with('success', 'Work category deleted successfully.');
    }
}
