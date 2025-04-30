<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Handle the dashboard redirection based on role.
     */
    public function index()
    {
        if (Gate::allows('admin')) {
            return view('dashboard.admin');
        }

        if (Gate::allows('chairperson')) {
            return view('dashboard.chairperson');
        }

        if (Gate::allows('dean')) {
            return view('dashboard.dean');
        }

        if (Gate::allows('instructor')) {
            return view('dashboard.instructor');
        }

        abort(403, 'Unauthorized access.');
    }
}
