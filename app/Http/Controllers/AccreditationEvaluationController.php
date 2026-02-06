<?php

namespace App\Http\Controllers;

use App\Models\AccreditationEvaluation;
use App\Models\AreaRecommendation;
use App\Models\ADMIN\Parameter;
use App\Models\ADMIN\Area;
use App\Models\ADMIN\ProgramAreaMapping;
use App\Models\ADMIN\AccreditationAssignment;
use App\Models\RatingOptions;
use App\Models\SubparameterRating;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccreditationEvaluationController extends Controller
{
    /* =========================================================
     | INDEX – LIST ALL EVALUATIONS
     ========================================================= */
    public function index()
    {
        $user = auth()->user();

        $query = AccreditationEvaluation::with([
            'accreditationInfo',
            'level',
            'program',
            'evaluator',
            'areaRecommendations.area',
        ]);

        // ===============================
        // ROLE-BASED VISIBILITY
        // ===============================

        // INTERNAL ASSESSOR → only evaluations they made
        if ($user->user_type === UserType::INTERNAL_ASSESSOR) {
            $query->where('evaluated_by', $user->id);
        }

        // ACCREDITOR → only evaluations they made
        if ($user->user_type === UserType::ACCREDITOR) {
            $query->where('evaluated_by', $user->id);
        }

        // ADMIN → sees everything (no filter)

        $evaluations = $query
            ->get()
            ->groupBy(fn ($e) =>
                $e->accred_info_id.'-'.$e->level_id.'-'.$e->program_id
            );

        return view('admin.accreditors.evaluations', compact('evaluations'));
    }

    /* =========================================================
     | SHOW AREA EVALUATION FORM (GET)
     | This loads the checklist UI
     ========================================================= */
    public function evaluateArea(
        int $infoId,
        int $levelId,
        int $programId,
        int $programAreaId
    ) {
        $programArea = Area::with('area')->findOrFail($programAreaId);

        // Check if this area is already evaluated
        $alreadyEvaluated = AreaRecommendation::query()
            ->where('area_id', $programAreaId)
            ->whereHas('evaluation', function ($q) use ($infoId, $levelId, $programId) {
                $q->where('accred_info_id', $infoId)
                  ->where('level_id', $levelId)
                  ->where('program_id', $programId);
            })
            ->exists();

        // Load parameters & subparameters for this area
        $parameters = Parameter::with('sub_parameters')
            ->where('area_id', $programArea->area_id)
            ->get();

        return view('admin.accreditors.internal-accessor-parameter', [
            'programArea'      => $programArea,
            'parameters'       => $parameters,
            'infoId'           => $infoId,
            'levelId'          => $levelId,
            'programId'        => $programId,
            'programAreaId'    => $programAreaId,
            'alreadyEvaluated' => $alreadyEvaluated,
        ]);
    }

    /* =========================================================
     | STORE – SAVE AREA EVALUATION (POST)
     ========================================================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'accred_info_id'  => ['required', 'exists:accreditation_infos,id'],
            'level_id'        => ['required', 'exists:accreditation_levels,id'],
            'program_id'      => ['required', 'exists:programs,id'],
            'program_area_id' => ['required', 'exists:areas,id'],
            'evaluations'     => ['required', 'array'],
            'recommendation'  => ['nullable', 'string'],
        ]);

        $user = auth()->user();
        $isAccreditor = $user->user_type === UserType::ACCREDITOR;

        // Accreditor can only evaluate once
        if ($isAccreditor) {
            $alreadyEvaluated = AreaRecommendation::query()
                ->where('area_id', $validated['program_area_id'])
                ->whereHas('evaluation', function ($q) use ($validated) {
                    $q->where('accred_info_id', $validated['accred_info_id'])
                    ->where('level_id', $validated['level_id'])
                    ->where('program_id', $validated['program_id']);
                })
                ->exists();

            if ($alreadyEvaluated) {
                return response()->json([
                    'message' => 'Accreditor can only evaluate once.'
                ], 409);
            }
        }

        $evaluation = DB::transaction(function () use ($validated) {

            $evaluation = AccreditationEvaluation::updateOrCreate(
    [
                    'accred_info_id' => $validated['accred_info_id'],
                    'level_id'       => $validated['level_id'],
                    'program_id'     => $validated['program_id'],
                    'area_id'        => $validated['program_area_id'],
                    'evaluated_by' => auth()->id(),
                ],
                [
                ]
            );

            foreach ($validated['evaluations'] as $subId => $data) {
                SubparameterRating::updateOrCreate(
                    [
                        'evaluation_id'   => $evaluation->id,
                        'subparameter_id' => $subId,
                    ],
                    [
                        'rating_option_id' =>
                            $this->mapStatusToRatingOption($data['status']),
                        'score' => $data['score'],
                    ]
                );
            }

            AreaRecommendation::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'area_id'       => $validated['program_area_id'],
                ],
                [
                    'recommendation' => $validated['recommendation'],
                ]
            );

            $evaluation->touch();

            return $evaluation;
        });

        return response()->json([
        'message' => 'Evaluation saved successfully.',
        'redirect' => route(
                'program.areas.evaluation.summary',
                [
                    'evaluation'     => $evaluation->id,
                    'area'  => $validated['program_area_id'],
                ]
            )
        ]);
    }

    /* =========================================================
     | SHOW SINGLE EVALUATION
     ========================================================= */
    public function show(
        AccreditationEvaluation $evaluation,
        Area $area
    )
    {
        $user = auth()->user();

        // ACCESS CONTROL
        if (
            $user->user_type === UserType::ACCREDITOR &&
            $evaluation->evaluated_by !== $user->id
        ) {
            abort(403, 'You are not allowed to view this evaluation.');
        }

        if ($user->user_type === UserType::TASK_FORCE) {
            $assigned = AccreditationAssignment::where('user_id', $user->id)
                ->where('area_id', $area->id)
                ->where('program_id', $evaluation->program_id)
                ->where('level_id', $evaluation->level_id)
                ->where('accred_info_id', $evaluation->accred_info_id)
                ->exists();

            if (! $assigned) {
                abort(403, 'You are not assigned to this area.');
            }
        }

        // Admin & Task Force are always allowed
        if (
            ! in_array($user->user_type, [
                UserType::ADMIN,
                UserType::TASK_FORCE,
                UserType::INTERNAL_ASSESSOR,
                UserType::ACCREDITOR,
            ])
        ) {
            abort(403);
        }

        // Load all required relationships
        $evaluation->load([
            'accreditationInfo',
            'level',
            'program',
            'evaluator',
            'subparameterRatings.ratingOption',
            'areaRecommendations.area',
        ]);

        // Resolve the evaluated AREA
        $areaRecommendation = $evaluation->areaRecommendations()
            ->where('area_id', $area->id)
            ->firstOrFail();

        // Load parameters + subparameters ONLY for this area
        $parameters = Parameter::with('sub_parameters')
            ->where('area_id', $area->id)
            ->get();

        // Collect ALL subparameter IDs for this area
        $subparameterIds = $parameters
            ->flatMap(fn ($parameter) => $parameter->sub_parameters->pluck('id'))
            ->values();

        // Filter ratings → ONLY ratings belonging to this area
        $ratings = $evaluation->subparameterRatings
            ->whereIn('subparameter_id', $subparameterIds)
            ->keyBy('subparameter_id');

        // Initialize totals
        $totals = [
            'available'       => 0,
            'inadequate'      => 0,
            'not_available'   => 0,
            'not_applicable'  => 'N/A',
        ];

        $totalScore = 0;
        $applicableCount = 0;

        // Compute totals + mean (mirrors Alpine compute())
        foreach ($ratings as $rating) {
            $label = $rating->ratingOption->label;

            if (in_array($label, ['Available', 'Available but Inadequate'])) {
                $totalScore += $rating->score;
                $applicableCount++;

                if ($label === 'Available') {
                    $totals['available'] += $rating->score;
                } else {
                    $totals['inadequate'] += $rating->score;
                }

            } elseif ($label === 'Not Available') {
                $applicableCount++;
            }

            // Not Applicable → ignored entirely
        }

        // Area mean
        $mean = $applicableCount
            ? number_format($totalScore / $applicableCount, 2)
            : '0.00';

         // ===============================
        // PREV / NEXT AREA (evaluated only)
        // ===============================
        $areaIds = $evaluation->areaRecommendations()
            ->orderBy('area_id')
            ->pluck('area_id')
            ->values();

        $currentIndex = $areaIds->search($area->id);

        $prevArea = ($currentIndex !== false && $currentIndex > 0)
            ? Area::find($areaIds[$currentIndex - 1])
            : null;

        $nextArea = ($currentIndex !== false && $currentIndex < $areaIds->count() - 1)
            ? Area::find($areaIds[$currentIndex + 1])
            : null;

        // 9. Render immutable summary view
        return view('admin.accreditors.show-evaluation', compact(
           'evaluation',
            'area',
            'parameters',
            'ratings',
            'totals',
            'mean',
            'prevArea',
            'nextArea'
        ));
    }

    /* =========================================================
     | EDIT
     ========================================================= */
    public function edit(AccreditationEvaluation $accreditationEvaluation)
    {
        return view(
            'accreditation_evaluations.edit',
            compact('accreditationEvaluation')
        );
    }

    /* =========================================================
     | UPDATE
     ========================================================= */
    public function update(
        Request $request,
        AccreditationEvaluation $accreditationEvaluation
    ) {
        $validated = $request->validate([
            'level_id'   => ['required', 'exists:accreditation_levels,id'],
            'program_id' => ['required', 'exists:programs,id'],
        ]);

        $accreditationEvaluation->update($validated);

        return redirect()
            ->route('accreditation-evaluations.show', $accreditationEvaluation)
            ->with('success', 'Evaluation updated successfully.');
    }

    /* =========================================================
     | DELETE
     ========================================================= */
    public function destroy(AccreditationEvaluation $accreditationEvaluation)
    {
        $accreditationEvaluation->delete();

        return redirect()
            ->route('accreditation-evaluations.index')
            ->with('success', 'Evaluation deleted successfully.');
    }

    /* =========================================================
     | HELPER – MAP UI STATUS TO RATING OPTION ID
     ========================================================= */
    private function mapStatusToRatingOption(string $status): int
    {
        return match ($status) {
            'available'      =>
                RatingOptions::where('label', 'Available')->value('id'),

            'inadequate'     =>
                RatingOptions::where('label', 'Available but Inadequate')->value('id'),

            'not_available'  =>
                RatingOptions::where('label', 'Not Available')->value('id'),

            'not_applicable' =>
                RatingOptions::where('label', 'Not Applicable')->value('id'),

            default =>
                throw new \InvalidArgumentException("Unknown status: {$status}")
        };
    }
}
