<?php

namespace App\Http\Controllers;

use App\Enums\UserType;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleName = $user->currentRole?->name;

        $roleView = match ($roleName) {
            'ADMIN' => 'admin',
            'DEAN' => 'dean',
            'TASK FORCE' => 'taskforce',
            'INTERNAL ASSESSOR' => 'assessor',
            'ACCREDITOR' => 'accreditor',
            default => '',
        };

        return view('admin.dashboard.index', compact('roleView'));
    }
}
