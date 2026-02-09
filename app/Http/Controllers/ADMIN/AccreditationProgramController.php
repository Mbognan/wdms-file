<?php

namespace App\Http\Controllers\ADMIN;

use App\Models\ADMIN\InfoLevelProgramMapping;
use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        //
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
