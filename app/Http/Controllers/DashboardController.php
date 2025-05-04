<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Handle the dashboard redirection based on role.
     */
    public function index()
    {
        if (Gate::allows('instructor')) {
            if (!session()->has('active_academic_period_id')) {
                return redirect()->route('select.academicPeriod');
            }
            return view('dashboard.instructor');
        }

        if (Gate::allows('chairperson')) {
            if (!session()->has('active_academic_period_id')) {
                return redirect()->route('select.academicPeriod');
            }
            return view('dashboard.chairperson');
        }

        if (Gate::allows('admin')) {
            return view('dashboard.admin');
        }

        if (Gate::allows('dean')) {
            return view('dashboard.dean');
        }

        abort(403, 'Unauthorized access.');
    }
}
