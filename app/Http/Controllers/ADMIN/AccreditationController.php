<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Models\ADMIN\ProgramAreaMapping;
use App\Models\AreaEvaluation;
use App\Models\AreaEvaluationFile;
use App\Models\ProgramFinalVerdict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccreditationController extends Controller
{
   public function store(
    Request $request,
    int $infoId,
    int $levelId,
    int $programId,
    int $programAreaId
) {


    $request->validate([
        'files' => 'required|array',
        'files.*' => 'file|max:10240',
        'status' => 'required|in:revision,completed,not_completed,ongoing',
    ]);




    $programArea = ProgramAreaMapping::findOrFail($programAreaId);


    $evaluation = AreaEvaluation::firstOrCreate(
        ['program_area_mapping_id' => $programAreaId],
        [
            'internal_accessor_id' => Auth::id(),
            'status' => 'ongoing',
        ]
    );


    $evaluation->update([
        'status' => $request->status,
        'internal_accessor_id' => Auth::id(),
        'completed_at' => $request->status === 'completed'
            ? now()
            : null,
    ]);



    // 📂 STORE FILES (SAME STYLE AS SUBPARAMETER UPLOADS)
    foreach ($request->file('files') as $file) {

        $path = $file->store(
            "area_evaluations/{$infoId}/{$levelId}/{$programId}/{$programAreaId}",
            'public'
        );



        AreaEvaluationFile::create([
            'area_evaluation_id' => $evaluation->id,
            'uploaded_by' => Auth::id(),

            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);


    }



    return back()->with('success', 'Area evaluation uploaded successfully.');
}


public function showAreaEvaluation(
    int $infoId,
    int $levelId,
    int $programId,
    int $programAreaId
) {
    $programArea = ProgramAreaMapping::with([
        'area',
        'users',
        'parameters.sub_parameters',
    ])->findOrFail($programAreaId);

    // 🔑 Get evaluation with files
    $evaluation = AreaEvaluation::with('files.uploader')
        ->where('program_area_mapping_id', $programAreaId)
        ->first();

    $parameters = $programArea->parameters;

    return view('admin.accreditors.internal-accessor-parameter', compact(
        'infoId',
        'levelId',
        'programId',
        'programAreaId',
        'programArea',
        'parameters',
        'evaluation'
    ));
}
  public function storeFinalVerdict(Request $request)
    {
        $request->validate([
            'program_id'     => 'required|exists:programs,id',
            'accred_info_id' => 'required|exists:accreditation_infos,id',
            'level_id'       => 'required|exists:accreditation_levels,id',
            'status'         => 'required|in:revisit,completed',
            'comments'       => 'required|string',
        ]);

        ProgramFinalVerdict::updateOrCreate(
            [
                'program_id'     => $request->program_id,
                'accred_info_id' => $request->accred_info_id,
            ],
            [
                'current_level_id' => $request->level_id,
                'status'           => $request->status,
                'comments'         => $request->comments,
                'level_up'         => null, // 👈 intentionally blank
                'revisit_until'    => null, // 👈 handle later if needed
                'decided_by'       => auth()->id(),
                'finalized_at'     => now(),
            ]
        );

        return response()->json([
            'message' => 'Final verdict saved successfully'
        ]);
    }
}
