<?php

namespace App\Http\Controllers\Chairperson;

use App\Http\Controllers\Controller;
use App\Models\UnverifiedUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountApprovalController extends Controller
{
    /**
     * Display a list of all pending instructor accounts for approval.
     */
    public function index(): View
    {
        // Eager-load related department and course for display
        $pendingAccounts = UnverifiedUser::with(['department', 'course'])->get();

        return view('chairperson.manage-instructors', compact('pendingAccounts'));
    }

    /**
     * Approve a pending instructor and migrate their data to the main users table.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        $pending = UnverifiedUser::findOrFail($id);

        // Transfer to the main users table
        User::create([
            'first_name'    => $pending->first_name,
            'middle_name'   => $pending->middle_name,
            'last_name'     => $pending->last_name,
            'email'         => $pending->email,
            'password'      => $pending->password, // Already hashed
            'department_id' => $pending->department_id,
            'course_id'     => $pending->course_id,
            'role'          => 0, // Instructor role
            'is_active'     => true,
        ]);

        // Remove from unverified list
        $pending->delete();

        return back()->with('status', 'Instructor account has been approved successfully.');
    }

    /**
     * Reject and delete a pending instructor account request.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function reject(int $id): RedirectResponse
    {
        $pending = UnverifiedUser::findOrFail($id);
        $pending->delete();

        return back()->with('status', 'Instructor account request has been rejected and removed.');
    }
}
