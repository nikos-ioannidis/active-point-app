<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingController extends Controller
{
    /**
     * Display a listing of the trainings.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $trainings = Training::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Trainings/Index', [
            'trainings' => $trainings,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new training.
     */
    public function create()
    {
        return Inertia::render('Trainings/Create');
    }

    /**
     * Store a newly created training in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_standard' => 'required|numeric|min:0',
            'max_hours' => 'required|integer|min:0',
            'is_irata' => 'boolean',
        ]);

        Training::create($validated);

        return redirect()->route('trainings.index')
            ->with('success', 'Training created successfully.');
    }

    /**
     * Display the specified training.
     */
    public function show(Training $training)
    {
        return Inertia::render('Trainings/Show', [
            'training' => $training,
        ]);
    }

    /**
     * Show the form for editing the specified training.
     */
    public function edit(Training $training)
    {
        return Inertia::render('Trainings/Edit', [
            'training' => $training,
        ]);
    }

    /**
     * Update the specified training in storage.
     */
    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_standard' => 'required|numeric|min:0',
            'max_hours' => 'required|integer|min:0',
            'is_irata' => 'boolean',
        ]);

        $training->update($validated);

        return redirect()->route('trainings.index')
            ->with('success', 'Training updated successfully.');
    }

    /**
     * Remove the specified training from storage.
     */
    public function destroy(Training $training)
    {
        $training->delete();

        return redirect()->route('trainings.index')
            ->with('success', 'Training deleted successfully.');
    }
}
