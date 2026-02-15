<?php

namespace App\Http\Controllers;

use App\Models\ADMIN\AccreditationInfo;
use App\Models\ADMIN\InfoLevelProgramMapping;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()
    {
        return view('admin.accreditors.archive');
    }

     public function completed()
    {
        /**
         * Get accreditations that have
         * at least ONE program with final verdict = completed
         */
        $accreditations = AccreditationInfo::whereHas('finalVerdicts', function ($q) {
                $q->where('status', 'completed');
            })
            ->withCount([
                // count completed programs
                'finalVerdicts as completed_programs_count' => function ($q) {
                    $q->where('status', 'completed');
                }
            ])
            ->orderByDesc('year')
            ->get();

        return view(
            'admin.accreditors.archive-complete',
            compact('accreditations')
        );
    }

    public function deleted()
    {
        $deletedPrograms = InfoLevelProgramMapping::onlyTrashed()
            ->with([
                'accreditationInfo',
                'level',
                'program',
                'deletedBy',
            ])
            ->latest('deleted_at')
            ->get();

        return view(
            'admin.accreditors.archive-deleted',
            compact('deletedPrograms')
        );
    }
}
