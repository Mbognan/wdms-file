<?php

namespace App\Http\Controllers\ADMIN;

use App\Http\Controllers\Controller;
use App\Models\ADMIN\AccreditationAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTaskForceController extends Controller
{
    public function index()
    {
        return view('admin.users.taskforce');
    }

    /**
     * Datatable data for Task Force users
     */
    public function data()
    {
        $users = User::whereIn('user_type', [
            'TASK FORCE',
            'TASK FORCE CHAIR'
        ])
            ->where('status', 'Active')
            ->latest()
            ->get();

        return response()->json([
            'data' => $users
        ]);
    }

    public function viewTaskForce($id)
    {
        $user = User::findOrFail($id);


        $assignments = AccreditationAssignment::with([
            'accreditationInfo',
            'program',
            'area',
            'level',
        ])
            ->where('user_id', $user->id)
            ->get();

        if ($assignments->isEmpty()) {
            return view('admin.users.viewtaskforce', [
                'user' => $user,
                'assignmentHierarchy' => [],
            ]);
        }
        $documents = \App\Models\ADMIN\AccreditationDocuments::with('uploader')
            ->where('upload_by', $user->id)
            ->get()
            ->groupBy(function ($doc) {
                return implode('-', [
                    $doc->accred_info_id,
                    $doc->level_id,
                    $doc->program_id,
                    $doc->area_id,
                    $doc->parameter_id,
                    $doc->subparameter_id,
                ]);
            });


        $infoProgramMappings = \App\Models\ADMIN\InfoLevelProgramMapping::with([
            'programAreas.area',
            'programAreas.areaParameterMappings.parameter.sub_parameters',
        ])->get()->keyBy(function ($map) {
            return $map->accreditation_info_id . '-' . $map->level_id . '-' . $map->program_id;
        });

        $assignmentHierarchy = [];


        foreach ($assignments as $assignment) {

            $key = $assignment->accred_info_id . '-' .
                $assignment->level_id . '-' .
                $assignment->program_id;

            $infoMap = $infoProgramMappings[$key] ?? null;

            if (!$infoMap)
                continue;

            foreach ($infoMap->programAreas as $programArea) {


                if ($programArea->area_id !== $assignment->area_id) {
                    continue;
                }

                $accId = $assignment->accreditationInfo->id;
                $progId = $assignment->program->id;
                $areaId = $programArea->id;


                $assignmentHierarchy[$accId]['title']
                    = $assignment->accreditationInfo->title;


                $assignmentHierarchy[$accId]['programs'][$progId]['name']
                    = $assignment->program->program_name;


                $assignmentHierarchy[$accId]['programs'][$progId]['areas'][$areaId]['name']
                    = $programArea->area->area_name;


                foreach ($programArea->areaParameterMappings as $apm) {

                    if (!$apm->parameter)
                        continue;

                    $paramId = $apm->parameter->id;

                    $assignmentHierarchy[$accId]['programs'][$progId]['areas'][$areaId]
                    ['parameters'][$paramId]['name']
                        = $apm->parameter->parameter_name;

                    foreach ($apm->subParameters as $sub) {

                        $docKey = implode('-', [
                            $assignment->accred_info_id,
                            $assignment->level_id,
                            $assignment->program_id,
                            $assignment->area_id,
                            $paramId,
                            $sub->id,
                        ]);

                        $uploadedDocs = $documents[$docKey] ?? collect();

                        $assignmentHierarchy[$accId]['programs'][$progId]['areas'][$areaId]
                        ['parameters'][$paramId]['sub_parameters'][$sub->id] = [
                            'name' => $sub->sub_parameter_name,
                            'documents' => $uploadedDocs->map(function ($doc) {
                                return [
                                    'id' => $doc->id,
                                    'file_name' => $doc->file_name,
                                    'file_path' => $doc->file_path,
                                    'file_type' => $doc->file_type,
                                    'uploaded_by' => optional($doc->uploader)->name,
                                    'status' => 'Submitted', // static for now
                                ];
                            })->values(),
                        ];
                    }

                }
            }
        }


        return view('admin.users.viewtaskforce', [
            'user' => $user,
            'assignmentHierarchy' => $assignmentHierarchy,
        ]);
    }




}
