<?php

namespace App\Http\Controllers\ADMIN;

use App\Models\ADMIN\InfoLevelProgramMapping;
use App\Http\Controllers\Controller;
use App\Models\ADMIN\Program;
use Illuminate\Http\Request;

class AccreditationProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // app/Http/Controllers/ADMIN/AdminAcreditationController.php

    public function store(Request $request)
    {
        $validated = $request->validate([
            'accreditation_info_id' => 'required|exists:accreditation_infos,id',
            'level_id'              => 'required|exists:accreditation_levels,id',
            'program_name'          => 'required|string|max:255',
        ]);

        $exists = InfoLevelProgramMapping::where('accreditation_info_id', $validated['accreditation_info_id'])
            ->where('level_id', $validated['level_id'])
            ->whereHas('program', function ($q) use ($validated) {
                $q->whereRaw('LOWER(program_name) = ?', [strtolower($validated['program_name'])]);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Program already exists in this level.'
            ], 422);
        }

        // create or find program
        $program = Program::firstOrCreate([
            'program_name' => $validated['program_name']
        ]);

        $mapping = InfoLevelProgramMapping::create([
            'accreditation_info_id' => $validated['accreditation_info_id'],
            'level_id'              => $validated['level_id'],
            'program_id'            => $program->id,
        ]);

        return response()->json([
            'message' => 'Program added successfully.',
            'data' => [
                'mapping_id'   => $mapping->id,
                'program_name' => $program->program_name,
            ]
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InfoLevelProgramMapping $mapping)
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
        ]);

        $mapping->program->update([
            'program_name' => $request->program_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully.',
            'data' => [
                'mapping_id' => $mapping->id,
                'program_name' => $mapping->program->program_name,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InfoLevelProgramMapping $mapping)
    {
        // SOFT DELETE
        $mapping->delete(); 

        return response()->json([
            'success' => true,
            'message' => 'Program removed from this accreditation.',
            'data' => [
                'mapping_id' => $mapping->id,
            ]
        ]);
    }
}
