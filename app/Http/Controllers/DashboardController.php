<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return Inertia::render('Dashboard-Admin');
        } elseif ($user->hasRole('Technician')) {
            return Inertia::render('Dashboard-Technician');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
