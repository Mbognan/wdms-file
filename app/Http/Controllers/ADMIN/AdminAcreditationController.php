<?php

namespace App\Http\Controllers\ADMIN;

use App\Enums\EvaluationStatus;
use App\Enums\TaskForceRole;
use App\Enums\UserStatus;
use App\Enums\VisitType;
use App\Http\Controllers\Controller;
use App\Models\AccreditationEvaluation;
use App\Models\ADMIN\AccreditationAssignment;
use App\Models\ADMIN\AccreditationBody;
use App\Models\ADMIN\AccreditationDocuments;
use App\Models\ADMIN\AccreditationInfo;
use App\Models\ADMIN\AccreditationLevel;
use App\Models\ADMIN\Area;
use App\Models\ADMIN\AreaParameterMapping;
use App\Models\ADMIN\InfoLevelProgramMapping;
use App\Models\ADMIN\Parameter;
use App\Models\ADMIN\Program;
use App\Models\ADMIN\ProgramAreaMapping;
use App\Models\ADMIN\SubParameter;
use App\Models\AreaEvaluation;
use App\Models\Role;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class AdminAcreditationController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $isAdmin = $user?->user_type === UserType::ADMIN;
        $isInternalAssessor = $user?->user_type === UserType::INTERNAL_ASSESSOR;

        return view(
            'admin.accreditors.acrreditation',
            compact('isAdmin', 'isInternalAssessor')
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
            'accreditation_body' => 'required',
            'visit_type' => ['required', Rule::enum(VisitType::class)]
        ]);

        DB::transaction(function () use ($request) {

            // Accreditation Body
            $body = AccreditationBody::firstOrCreate([
                'name' => $request->accreditation_body
            ]);

            // Accreditation Info
            $accreditation = AccreditationInfo::create([
                'title' => $request->title,
                'year' => Carbon::parse($request->date)->year,
                'status' => 'ongoing',
                'visit_type' => $request->visit_type,
                'accreditation_body_id' => $body->id,
                'accreditation_date' => $request->date
            ]);

            // Level (SINGLE)
            $level = AccreditationLevel::firstOrCreate([
                'level_name' => $request->level
            ]);

            // Programs (MULTIPLE)
            foreach ($request->programs as $programName) {

                $program = Program::firstOrCreate([
                    'program_name' => $programName
                ]);

                InfoLevelProgramMapping::create([
                    'accreditation_info_id' => $accreditation->id,
                    'level_id' => $level->id,
                    'program_id' => $program->id,
                ]);
            }
        });

        return back()->with('success', 'Accreditation saved successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $isAdmin = $user->user_type === UserType::ADMIN;
        $accreditation = AccreditationInfo::with('accreditationBody')->findOrFail($id);

        $levels = InfoLevelProgramMapping::with(['level', 'program'])
            ->where('accreditation_info_id', $id)
            ->get()
            ->groupBy('level_id');

        return view('admin.accreditors.show-accreditation', [
            'accreditation' => $accreditation,
            'levels' => $levels,
            'isAdmin' => $isAdmin
        ]);
    }

    public function edit($id)
    {
        $accreditation = AccreditationInfo::with('accreditationBody')->findOrFail($id);

        return response()->json([
            'id' => $accreditation->id,
            'title' => $accreditation->title,

            'date' => $accreditation->accreditation_date
                ? Carbon::parse($accreditation->accreditation_date)->format('Y-m-d')
                : null,

            'accreditation_body' => $accreditation->accreditationBody?->name,

            'visit_type' => strtolower($accreditation->visit_type),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'date' => 'required|date',
            'accreditation_body' => 'required|string',
            'visit_type' => ['required', Rule::enum(VisitType::class)],
        ]);

        $accreditation = null;
        $body = null;

        DB::transaction(function () use ($request, $id, &$accreditation, &$body) {

            // Accreditation Body
            $body = AccreditationBody::firstOrCreate([
                'name' => $request->accreditation_body
            ]);

            // Accreditation Info
            $accreditation = AccreditationInfo::findOrFail($id);

            $accreditation->update([
                'title' => $request->title,
                'year' => Carbon::parse($request->date)->year,
                'accreditation_body_id' => $body->id,
                'accreditation_date' => $request->date,
                'visit_type' => $request->visit_type,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Accreditation updated successfully.',
            'data' => [
                'title' => $accreditation->title,
                'visit_type' => $accreditation->visit_type,
                'accreditation_body' => $body->name,
                'accreditation_date' =>
                    optional($accreditation->accreditation_date)->format('F d, Y'),
            ]
        ]);
    }


    public function addLevelWithPrograms(Request $request)
    {
        $request->validate([
            'accreditation_info_id' => 'required|exists:accreditation_infos,id',
            'level' => 'required|string',
            'programs' => 'required|array|min:1',
            'programs.*' => 'required|string'
        ]);

        DB::transaction(function () use ($request) {


            $level = AccreditationLevel::firstOrCreate([
                'level_name' => $request->level
            ]);

            foreach ($request->programs as $programName) {

                // Ensure Program exists
                $program = Program::firstOrCreate([
                    'program_name' => $programName
                ]);

                // Prevent duplicate mapping
                InfoLevelProgramMapping::firstOrCreate([
                    'accreditation_info_id' => $request->accreditation_info_id,
                    'level_id' => $level->id,
                    'program_id' => $program->id,
                ]);
            }
        });

        return response()->json([
            'message' => 'Level and programs added successfully.'
        ], 200);
    }
    public function addProgramOnly(Request $request)
    {
        $request->validate([
            'accreditation_info_id' => 'required|exists:accreditation_infos,id',
            'level' => 'required|string',
            'programs' => 'required|array|min:1',
            'programs.*' => 'required|string'
        ]);

        DB::transaction(function () use ($request) {

            $level = AccreditationLevel::firstOrCreate([
                'level_name' => $request->level
            ]);

            foreach ($request->programs as $programName) {

                $program = Program::firstOrCreate([
                    'program_name' => $programName
                ]);

                InfoLevelProgramMapping::firstOrCreate([
                    'accreditation_info_id' => $request->accreditation_info_id,
                    'level_id' => $level->id,
                    'program_id' => $program->id,
                ]);
            }
        });

        return response()->json([
            'message' => 'Program(s) added successfully.'
        ], 200);
    }


    public function getAccreditations()
    {
        $user = auth()->user();
        $isAdmin = $user->user_type === UserType::ADMIN;
        $isDean = $user->user_type === UserType::DEAN;

        $levelOrder = [
            'PRELIMINARY' => 1,
            'LEVEL I' => 2,
            'LEVEL II' => 3,
            'LEVEL III' => 4,
            'LEVEL IV' => 5,
        ];

        if ($isAdmin || $isDean) {
            // Admin sees all mappings
            $mappings = InfoLevelProgramMapping::with([
                'accreditationInfo.accreditationBody',
                'level',
                'program'
            ])->get();
        } else {
            // Get the user’s assignments
            $assignments = AccreditationAssignment::where('user_id', $user->id)
                ->select('accred_info_id', 'program_id', 'level_id')
                ->distinct()
                ->get();

            if ($assignments->isEmpty()) {
                return response()->json([]); // no assignments
            }

            // Get the list of mapping IDs for filtering
            $mappings = InfoLevelProgramMapping::with([
                'accreditationInfo.accreditationBody',
                'level',
                'program'
            ])->where(function ($query) use ($assignments) {
                foreach ($assignments as $a) {
                    $query->orWhere(function ($q) use ($a) {
                        $q->where('accreditation_info_id', $a->accred_info_id)
                            ->where('program_id', $a->program_id)
                            ->where('level_id', $a->level_id);
                    });
                }
            })->get();
        }

        // Group by Accreditation Body
        $grouped = $mappings
            ->groupBy(fn($item) => $item->accreditationInfo->accreditation_body_id)
            ->map(function ($bodyItems) use ($levelOrder) {
                $body = $bodyItems->first()->accreditationInfo->accreditationBody;

                $bodyAccreditationInfos = $bodyItems
                    ->groupBy('accreditation_info_id')
                    ->map(function ($infoItems) use ($levelOrder) {
                        $accreditationInfo = $infoItems->first()->accreditationInfo;

                        $programs = $infoItems->map(function ($p) use ($levelOrder) {
                            return [
                                'name' => $p->program->program_name,
                                'level' => strtoupper(trim($p->level->level_name)),
                                'level_id' => $p->level->id,
                                'status' => $p->accreditationInfo->status
                            ];
                        })->sortBy(fn($p) => $levelOrder[$p['level']] ?? 999)
                            ->values();

                        return [
                            'id' => $accreditationInfo->id,
                            'title' => $accreditationInfo->title,
                            'year' => $accreditationInfo->year,
                            'status' => $accreditationInfo->status,
                            'programs' => $programs
                        ];
                    })->values();

                return [
                    'body_name' => $body->name,
                    'body_status' => 'Active',
                    'accreditation_infos' => $bodyAccreditationInfos
                ];
            })->values();

        return response()->json($grouped);
    }


    public function showProgram($infoId, $levelId, $programName)
    {
        $user = auth()->user();

        $isAdmin = $user->currentRole->name === UserType::ADMIN->value;
        $isDean  = $user->currentRole->name === UserType::DEAN->value;

        $levelName = AccreditationLevel::where('id', $levelId)->value('level_name');

        $program = InfoLevelProgramMapping::where([
            'accreditation_info_id' => $infoId,
            'level_id' => $levelId,
        ])->whereHas('program', function ($q) use ($programName) {
            $q->where('program_name', $programName);
        })->firstOrFail();

        /**
         * ---------------------------------------
         * USERS TO SHOW BASED ON ROLE (pivot)
         * ---------------------------------------
         */
        if ($isAdmin) {
            // Show users who have "Internal Assessor" role
            $users = User::whereHas('roles', function ($q) {
                $q->where('name', UserType::INTERNAL_ASSESSOR->value);
            })->where('status', UserStatus::ACTIVE->value)->orderBy('name')->get();
        } elseif ($isDean) {
            // Show users who have "Task Force" role
            $users = User::whereHas('roles', function ($q) {
                $q->where('name', UserType::TASK_FORCE->value);
            })->where('status', UserStatus::ACTIVE->value)->orderBy('name')->get();
        } else {
            $users = collect(); // Other roles don't see additional users
        }

        $availableUsers = $users;

        /**
         * ---------------------------------------
         * PROGRAM AREAS
         * ---------------------------------------
         */
        if ($isAdmin || $isDean) {
            $programAreas = ProgramAreaMapping::with([
                'users' => function ($q) use ($isAdmin, $isDean) {
                    if ($isAdmin) {
                        $q->whereHas('roles', fn($q) => $q->where('name', 'Internal Assessor'));
                    } elseif ($isDean) {
                        $q->whereHas('roles', fn($q) => $q->where('name', 'Task Force'));
                    }
                    $q->orderBy('name');
                }
            ])->where('info_level_program_mapping_id', $program->id)->get();
        } else {
            // Non-admin/dean: filter areas first
            $assignedAreaIds = AccreditationAssignment::where([
                'user_id' => $user->id,
                'accred_info_id' => $infoId,
                'level_id' => $levelId,
                'program_id' => $program->program_id,
            ])->pluck('area_id')->unique()->values();

            $programAreas = ProgramAreaMapping::with(['users' => function ($q) use ($user) {
                // Only users with same role as logged-in user
                $roleName = $user->currentRole->name;
                $q->whereHas('roles', fn($q) => $q->where('name', $roleName));
                $q->orderBy('name');
            }])
            ->where('info_level_program_mapping_id', $program->id)
            ->whereIn('id', $assignedAreaIds)
            ->get();
        }

        return view('admin.accreditors.program', [
            'infoId' => $infoId,
            'level' => $levelName,
            'levelId' => $levelId,
            'programName' => $programName,
            'programId' => $program->program_id,
            'users' => $availableUsers,
            'programAreas' => $programAreas,
            'isAdmin' => $isAdmin,
            'isDean' => $isDean,
        ]);
    }

    public function getProgramAreas($programId)
    {
        $programAreas = ProgramAreaMapping::with('users', 'area')
            ->where('info_level_program_mapping_id', $programId)
            ->get()
            ->map(function ($pa) {
                return [
                    'id' => $pa->id,
                    'name' => $pa->area->area_name ?? 'N/A',
                    'users' => $pa->users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->toArray(),
                ];
            });

        return response()->json($programAreas);
    }


    public function saveAreas(Request $request, $programId)
    {
        \Log::info('saveAreas START', $request->all());

        DB::beginTransaction();

        try {

            /**
             * 1️⃣ GET EXISTING CONTEXT
             */
            $context = InfoLevelProgramMapping::where([
                'program_id' => $programId,
                'level_id' => $request->level_id,
                'accreditation_info_id' => $request->accreditation_info_id
            ])->first();

            if (!$context) {
                \Log::error('Context NOT FOUND', [
                    'program_id' => $programId,
                    'level_id' => $request->level_id,
                    'accreditation_info_id' => $request->accreditation_info_id
                ]);
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Program-Level-Accreditation mapping not found.'
                ], 404);
            }

            \Log::info('Context FOUND', [
                'context_id' => $context->id,
                'accreditation_info_id' => $context->accreditation_info_id,
                'level_id' => $context->level_id,
                'program_id' => $context->program_id
            ]);

            foreach ($request->areas as $areaData) {

                \Log::info('Processing Area', $areaData);

                /**
                 * 2️⃣ CREATE / GET AREA
                 */
                $area = Area::firstOrCreate([
                    'area_name' => $areaData['name']
                ]);

                \Log::info('Area OK', [
                    'area_id' => $area->id,
                    'area_name' => $area->area_name
                ]);

                /**
                 * 3️⃣ PROGRAM ↔ AREA MAPPING
                 */
                $programArea = ProgramAreaMapping::firstOrCreate([
                    'info_level_program_mapping_id' => $context->id,
                    'area_id' => $area->id
                ]);

                \Log::info('ProgramAreaMapping OK', [
                    'program_area_id' => $programArea->id,
                    'info_level_program_mapping_id' => $context->id,
                    'area_id' => $area->id
                ]);

                /**
                 * 4️⃣ CLEAR OLD ASSIGNMENTS
                 */
                AccreditationAssignment::where([
                    'accred_info_id' => $context->accreditation_info_id,
                    'level_id' => $context->level_id,
                    'program_id' => $context->program_id,
                    'area_id' => $programArea->id
                ])->delete();




                /**
                 * 5️⃣ ASSIGN USERS
                 */
                if (!empty($areaData['users'])) {
                    foreach ($areaData['users'] as $userId) {

                        $user = User::findOrFail($userId);
                        $currentRoleId = $user->currentRole->id;

                        // Prevent duplicate user in same area
                        $alreadyAssigned = AccreditationAssignment::where([
                            'user_id' => $userId,
                            'role_id' => $currentRoleId,
                            'accred_info_id' => $context->accreditation_info_id,
                            'level_id' => $context->level_id,
                            'program_id' => $context->program_id,
                            'area_id' => $programArea->id,
                        ])->exists();

                        if (!$alreadyAssigned) {
                            AccreditationAssignment::create([
                                'user_id' => $userId,
                                'role_id' => $currentRoleId,
                                'accred_info_id' => $context->accreditation_info_id,
                                'level_id' => $context->level_id,
                                'program_id' => $context->program_id,
                                'area_id' => $programArea->id,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            \Log::info('saveAreas SUCCESS');

            return response()->json([
                'success' => true,
                'message' => 'Areas & users saved successfully!'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error('saveAreas FAILED', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed saving areas'
            ], 500);
        }
    }
    public function assignUsersToArea(Request $request)
    {
        \Log::info('assignUsersToArea START', $request->all());

        $request->validate([
            'area_id' => 'required|exists:program_area_mappings,id',
            'program_id' => 'required|exists:programs,id',
            'level_id' => 'required|exists:accreditation_levels,id',
            'accreditation_info_id' => 'required|exists:accreditation_infos,id',
            'users' => 'array',
            'users.*.id' => 'exists:users,id',
            'users.*.role' => ['nullable', new Enum(TaskForceRole::class)]
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Get context
            $context = InfoLevelProgramMapping::where([
                'program_id' => $request->program_id,
                'level_id' => $request->level_id,
                'accreditation_info_id' => $request->accreditation_info_id,
            ])->firstOrFail();

            \Log::info('Context FOUND', ['context_id' => $context->id]);

            // 2️⃣ Get program area
            $programArea = ProgramAreaMapping::where('id', $request->area_id)
                ->where('info_level_program_mapping_id', $context->id)
                ->firstOrFail();



            // 4️⃣ Assign users (no duplicate check)
            if (!empty($request->users)) {
                
                foreach ($request->users as $userData) {

                    // 🔥 HANDLE ADMIN (simple array: users[])
                    if (is_numeric($userData)) {
                        $userId = $userData;
                        $role = TaskForceRole::MEMBER; // default role
                    } else {
                        // 🔥 HANDLE DEAN (users[id][role])
                        $userId = $userData['id'];
                        $role = isset($userData['role'])
                            ? TaskForceRole::from($userData['role'])
                            : TaskForceRole::MEMBER;
                    }

                    $user = User::findOrFail($userId);
                    $currentRoleId = $user->currentRole->id;

                    // ✅ Only enforce chair rule if role exists
                    if ($role === TaskForceRole::CHAIR) {

                        $existingChair = AccreditationAssignment::where([
                            'accred_info_id' => $context->accreditation_info_id,
                            'level_id' => $context->level_id,
                            'program_id' => $context->program_id,
                            'area_id' => $programArea->id,
                            'role' => TaskForceRole::CHAIR,
                        ])->exists();

                        if ($existingChair) {
                            return response()->json([
                                'success' => false,
                                'message' => 'This area already has a chair.'
                            ], 422);
                        }
                    }

                    // ✅ Prevent duplicate assignment
                    $exists = AccreditationAssignment::where([
                        'user_id' => $userId,
                        'accred_info_id' => $context->accreditation_info_id,
                        'level_id' => $context->level_id,
                        'program_id' => $context->program_id,
                        'area_id' => $programArea->id,
                    ])->exists();

                    if ($exists) {
                        return response()->json([
                            'success' => false,
                            'message' => "{$user->name} is already assigned."
                        ], 422);
                    }

                    AccreditationAssignment::create([
                        'user_id' => $userId,
                        'role_id' => $currentRoleId,
                        'accred_info_id' => $context->accreditation_info_id,
                        'level_id' => $context->level_id,
                        'program_id' => $context->program_id,
                        'area_id' => $programArea->id,
                        'role' => $role,
                    ]);
                }
            }

            DB::commit();

            \Log::info('assignUsersToArea SUCCESS');

            
            $assignedUsers = AccreditationAssignment::where([
                'accred_info_id' => $context->accreditation_info_id,
                'level_id' => $context->level_id,
                'program_id' => $context->program_id,
                'area_id' => $programArea->id,
            ])->with('user')->get()->pluck('user');

            return response()->json([
                'message' => 'Users assigned successfully.',
                'area_id' => $programArea->id,
                'users' => $assignedUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                })->values(),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('assignUsersToArea FAILED', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed assigning users.'
            ], 500);
        }
    }

    public function showParameters(
        int $infoId,
        int $levelId,
        int $programId,
        int $programAreaId
    ) {
        $user = auth()->user();

        $roleName = $user->currentRole->name;

        $isAdmin            = $roleName === UserType::ADMIN->value;
        $isDean             = $roleName === UserType::DEAN->value;
        $isTaskForce        = $roleName === UserType::TASK_FORCE->value;
        $isInternalAssessor = $roleName === UserType::INTERNAL_ASSESSOR->value;
        $isAccreditor       = $roleName === UserType::ACCREDITOR->value;

        // ================= CONTEXT =================
        $context = InfoLevelProgramMapping::where([
            'accreditation_info_id' => $infoId,
            'level_id'              => $levelId,
            'program_id'            => $programId,
        ])->firstOrFail();

        // ================= PROGRAM AREA =================
        $programArea = ProgramAreaMapping::with([
            'area',
            'parameters.sub_parameters'
        ])
        ->where('id', $programAreaId)
        ->where('info_level_program_mapping_id', $context->id)
        ->firstOrFail();

        // ================= ASSIGNMENTS QUERY =================
        $assignmentsQuery = AccreditationAssignment::with([
            'user',
            'role'
        ])
        ->where([
            'accred_info_id' => $infoId,
            'level_id'       => $levelId,
            'program_id'     => $programId,
            'area_id'        => $programArea->area->id,
        ]);

        // ================= ROLE-BASED FILTERING (DATABASE LEVEL) =================

        if ($isAdmin) {
            $assignmentsQuery->whereHas('user.roles', function ($q) {
                $q->whereIn('name', [
                    UserType::INTERNAL_ASSESSOR->value,
                    UserType::ACCREDITOR->value
                ]);
            });
        }

        elseif ($isInternalAssessor) {
            $assignmentsQuery->whereHas('user.roles', function ($q) {
                $q->where('name', UserType::INTERNAL_ASSESSOR->value);
            });
        }

        elseif ($isDean || $isTaskForce) {
            $assignmentsQuery->whereHas('user.roles', function ($q) {
                $q->where('name', UserType::TASK_FORCE->value);
            });
        }

        $assignments = $assignmentsQuery->get();

        // ================= SORT TASK FORCE (Chair First) =================
        if ($isDean || $isTaskForce) {
            $assignments = $assignments->sortByDesc(fn ($a) =>
                strtolower($a->role?->value ?? '') === 'chair'
            );
        }

        return view('admin.accreditors.parameter', [
            'infoId'        => $infoId,
            'levelId'       => $levelId,
            'programId'     => $programId,
            'programAreaId' => $programAreaId,
            'context'       => $context,
            'programArea'   => $programArea,
            'assignments'   => $assignments,
            'parameters'    => $programArea->parameters,
            'isAdmin'       => $isAdmin,
            'isDean'        => $isDean,
            'isTaskForce'   => $isTaskForce,
            'isIA'          => $isInternalAssessor,
            'isAccreditor'  => $isAccreditor,
            'loggedInUser'  => $user
        ]);
    }


    public function storeParameters(Request $request, $programAreaMappingId)
    {
        // Validate the incoming request
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'parameters' => 'required|array|min:1',
            'parameters.*.name' => 'required|string|max:255',
            'parameters.*.sub_parameters.*' => 'nullable|string|max:255',
        ]);

        $parametersData = $request->input('parameters');

        DB::transaction(function () use ($parametersData, $programAreaMappingId, $request) {

            foreach ($parametersData as $paramData) {

                // Create the Parameter
                $parameter = Parameter::create([
                    'parameter_name' => $paramData['name'],
                    'area_id' => $request->input('area_id'),
                ]);

                // Map the Parameter to the Program Area
                $areaParamMapping = AreaParameterMapping::create([
                    'program_area_mapping_id' => $programAreaMappingId,
                    'parameter_id' => $parameter->id,
                ]);

                // If Sub-Parameters exist, create them and attach to mapping
                if (!empty($paramData['sub_parameters'])) {
                    foreach ($paramData['sub_parameters'] as $subName) {

                        // Skip empty sub-parameter names
                        if (trim($subName) === '')
                            continue;

                        $subParam = SubParameter::create([
                            'sub_parameter_name' => $subName,
                            'parameter_id' => $parameter->id,
                        ]);

                        // Attach sub-parameter to area mapping
                        $areaParamMapping->subParameters()->attach($subParam->id);
                    }
                }
            }
        });

        return response()->json([
            'message' => 'Parameters & Sub-Parameters added successfully'
        ]);
    }

    public function subParameterUploads(
        SubParameter $subParameter,
        int $infoId,
        int $levelId,
        int $programId,
        int $programAreaId
    ) {
        $subParameter->load(['parameter', 'uploads.uploader']);

        return view('admin.accreditors.sub-param', [
            'subParameter' => $subParameter,
            'parameter' => $subParameter->parameter,
            'uploads' => $subParameter->uploads,

            // pass context forward
            'infoId' => $infoId,
            'levelId' => $levelId,
            'programId' => $programId,
            'programAreaId' => $programAreaId,
        ]);
    }


    public function storeSubParameterUploads(
        Request $request,
        SubParameter $subParameter,
        int $infoId,
        int $levelId,
        int $programId,
        int $programAreaId
    ) {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240',
        ]);

        $user = auth()->user();

        foreach ($request->file('files') as $file) {

            $path = $file->store(
                "accreditation_uploads/{$programAreaId}/{$subParameter->id}",
                'public'
            );

            AccreditationDocuments::create([
                'subparameter_id' => $subParameter->id,
                'parameter_id' => $subParameter->parameter_id,
                'area_id' => $programAreaId,
                'program_id' => $programId,
                'level_id' => $levelId,
                'accred_info_id' => $infoId,
                'upload_by' => Auth::id(),
                'role_id' => $user->currentRole->id,

                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
            ]);
        }

        return back()->with('success', 'Files uploaded successfully.');
    }

    public function destroySubParameterUpload(AccreditationDocuments $upload)
    {
        // Optional: authorization check
        // abort_if(Auth::id() !== $upload->upload_by && !Auth::user()->isAdmin(), 403);

        // Delete file from storage
        if (Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }
        
        // Delete database record
        $upload->delete();

        return back()->with('success', 'File deleted successfully.');
    }


    //INTERNAL ASSESSOR
    public function indexInternalAccessor()
    {
        $user = auth()->user();

        /**
         * USER ROLES
         */
        $isAdmin = $user->currentRole === UserType::ADMIN->value;
        $isInternalAssessor = $user->currentRole === UserType::INTERNAL_ASSESSOR->value;
        $isAccreditor = $user->currentRole === UserType::ACCREDITOR->value;

        /**
         * UI FLAGS
         */
        $isAccreditationUI = true;
        $canEvaluate = $isAccreditor;

        $mappings = InfoLevelProgramMapping::with([
            'accreditationInfo',
            'level',
            'program',
            'programAreas.area',
            'programAreas.evaluations'
        ])
        ->whereHas('accreditationInfo', function ($q) {
            $q->where('status', 'ongoing');
        })
        ->get();

        $data = [];

        foreach ($mappings as $mapping) {

            $levelName = $mapping->level->level_name;

            /**
             * TOTAL PROGRAM AREAS
             */
            $totalAreas = $mapping->programAreas->count();

            /**
             * COMPLETED PROGRAM AREAS
             * A program area is completed if it has
             * at least ONE evaluation with status = completed
             */
            $completedAreas = $mapping->programAreas->filter(function ($programArea) {
                return $programArea->evaluations
                    ->where('status', 'completed')
                    ->count() > 0;
            })->count();

            /**
             * PROGRESS CALCULATION
             */
            $progress = $totalAreas > 0
                ? round(($completedAreas / $totalAreas) * 100)
                : 0;

            /**
             * INTERNAL ASSESSORS:
             * Only see FULLY completed programs
             */
            if ($isInternalAssessor && !$isAccreditor && $totalAreas === 0) {
                continue;
            }

            if (!isset($data[$levelName])) {
                $data[$levelName] = [
                    'level_id' => $mapping->level->id,
                    'programs' => [],
                ];
            }

            $data[$levelName]['programs'][] = [
                'program_id' => $mapping->program->id,
                'program_name' => $mapping->program->program_name,
                'accreditation_id' => $mapping->accreditationInfo->id,
                'accreditation_title' => $mapping->accreditationInfo->title,

                // UI
                'accreditation_status_label' => '',

                'total_areas' => $totalAreas,
                'evaluated_areas' => $completedAreas,
                'progress' => $progress,
            ];
        }

        return view(
            'admin.accreditors.internal-accessor',
            compact(
                'isAdmin',
                'isInternalAssessor',
                'isAccreditationUI',
                'canEvaluate',
                'data'
            )
        );
    }

    public function showProgramAreas(
        int $accreditationId,
        int $levelId,
        int $programId
    ) {
        $user = auth()->user();

        $isAdmin = $user->currentRole->name === UserType::ADMIN->value;
        $isDean = $user->currentRole->name === UserType::DEAN->value;
        $isInternalAssessor = $user->currentRole->name === UserType::INTERNAL_ASSESSOR->value;
        $isTaskForce = $user->currentRole->name === UserType::TASK_FORCE->value;

        // ================= PROGRAM =================
        $program = Program::findOrFail($programId);

        // ================= CONTEXT =================
        $context = InfoLevelProgramMapping::where([
            'accreditation_info_id' => $accreditationId,
            'level_id' => $levelId,
            'program_id' => $programId,
        ])->firstOrFail();

        // ================= BASE QUERY =================
        $programAreasQuery = ProgramAreaMapping::with([
            'area',

            'users' => function ($q) use ($user, $isAdmin, $isDean, $isInternalAssessor, $isTaskForce) {

                if ($isAdmin) {
                    // Admin: see all Internal Assessors
                    $q->whereHas('roles', fn($q) => $q->where('name', UserType::INTERNAL_ASSESSOR->value));
                } elseif ($isDean) {
                    // Dean: see all Task Forces
                    $q->whereHas('roles', fn($q) => $q->where('name', UserType::TASK_FORCE->value));
                } elseif ($isInternalAssessor) {
                    // Internal Assessor: see only Internal Assessors assigned to this area
                    $q->whereHas('roles', fn($q) => $q->where('name', UserType::INTERNAL_ASSESSOR->value))
                    ->wherePivot('user_id', $user->id)
                    ->orWhereHas('roles', fn($q) => $q->where('name', UserType::INTERNAL_ASSESSOR->value));
                } elseif ($isTaskForce) {
                    // Task Force: see only Task Forces assigned to this area
                    $q->whereHas('roles', fn($q) => $q->where('name', UserType::TASK_FORCE->value))
                    ->wherePivot('user_id', $user->id)
                    ->orWhereHas('roles', fn($q) => $q->where('name', UserType::TASK_FORCE->value));
                }

                $q->orderBy('name');
            },

            // Latest evaluation per area
            'evaluations' => function ($q) {
                $q->latest()->limit(1);
            },

            'evaluations.files.uploader',
            'users.roles',
        ])
        ->where('info_level_program_mapping_id', $context->id);

        // ================= AREA VISIBILITY =================
        if (!$isAdmin && !$isDean) {
            // Internal Assessors / Task Forces see only areas they are assigned to
            $programAreasQuery->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $programAreas = $programAreasQuery->get();

        // ================= RETURN VIEW =================
        return view('admin.accreditors.internal-accessor-areas', [
            'programName' => $program->program_name,
            'programAreas' => $programAreas,
            'levelId' => $levelId,
            'programId' => $programId,
            'infoId' => $accreditationId,
            'isInternalAssessor' => $isInternalAssessor,
            'isTaskForce' => $isTaskForce,
            'isAdmin' => $isAdmin,
            'isDean' => $isDean,
        ]);
    }

    public function showAreaEvaluation(
        int $infoId,
        int $levelId,
        int $programId,
        int $programAreaId
    ) {
        // ================= AUTH USER =================
        $user = auth()->user();

        // ================= CONTEXT VALIDATION =================
        $context = InfoLevelProgramMapping::where([
            'accreditation_info_id' => $infoId,
            'level_id' => $levelId,
            'program_id' => $programId,
        ])->firstOrFail();

        // ================= ACCESS CONTROL =================
        if ($user->currentRole->name === UserType::INTERNAL_ASSESSOR->value) {

            $isAssigned = ProgramAreaMapping::where('id', $programAreaId)
            ->where('info_level_program_mapping_id', $context->id)
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->exists();

            if (! $isAssigned) {
                abort(403, 'You are not assigned to this area.');
            }
        }

        // ================= PROGRAM AREA =================
        $programArea = ProgramAreaMapping::with([
            'area',

            // ONLY INTERNAL ASSESSORS ASSIGNED TO THIS AREA
            'users' => function ($q) use ($user) {
                $q->where('user_type', UserType::INTERNAL_ASSESSOR)

                // logged-in user FIRST
                ->orderByRaw('users.id = ? DESC', [$user->id])

                // then alphabetically
                ->orderBy('name');
            },

            'parameters.sub_parameters',
        ])
            ->where('id', $programAreaId)
            ->where('info_level_program_mapping_id', $context->id)
            ->firstOrFail();

        // ================= PARAMETERS =================
        $parameters = $programArea->parameters;

        // ================= EXISTING EVALUATION (FILE-BASED) =================
        $evaluation = AreaEvaluation::with([
            'ratings.subparameter',
            'files.uploader',
            'evaluator',
        ])
            ->where('program_area_mapping_id', $programAreaId)
            ->latest()
            ->first();

        // ================= EXISTING EVALUATION (SYSTEM-BASED) =================
        $currentUserEvaluation = AccreditationEvaluation::where([
            'accred_info_id' => $infoId,
            'level_id'       => $levelId,
            'program_id'     => $programId,
            'area_id'        => $programAreaId,
            'evaluated_by'   => $user->id,
        ])->with('subparameterRatings.ratingOption', 'areaRecommendations')->first();

        // Flag for lock warning
        $isSubmittedOrUpdated = false;
        if ($currentUserEvaluation) {
            $isSubmittedOrUpdated = in_array($currentUserEvaluation->status, [
                EvaluationStatus::SUBMITTED,
                EvaluationStatus::UPDATED
            ]);
        }

        $isFinal = $currentUserEvaluation?->is_final ?? false;

        $locked = $currentUserEvaluation ? true : false;

        // ================= USER ROLES =================
        $isAdmin = $user->currentRole->name === UserType::ADMIN->value;
        $isInternalAssessor = $user?->currentRole->name === UserType::INTERNAL_ASSESSOR->value;

        // ================= STATE FLAGS =================
        $isEvaluated = !is_null($evaluation);
        $isLocked = $isEvaluated && !$isAdmin;

        // ================= RETURN VIEW =================
        return view('admin.accreditors.internal-accessor-parameter', compact(
            'infoId',
            'levelId',
            'programId',
            'programAreaId',
            'context',
            'programArea',
            'parameters',
            'evaluation',
            'isEvaluated',
            'isLocked',
            'isAdmin',
            'isInternalAssessor',
            'currentUserEvaluation',
            'isSubmittedOrUpdated',
            'isFinal'
        ));
    }
}
