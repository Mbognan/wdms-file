<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('status', ['pending'])->get();

        return view('admin.users.index', compact('users'));
    }


    public function data()
    {
        $users = User::select([
            'id',
            'name',
            'email',
            'user_type',
            'status',
            'created_at'
        ])
           ->where('status', 'Pending')->get();

        return response()->json([
            'data' => $users
        ]);
    }
    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'Suspended';
        $user->save();

        return response()->json([
            'message' => 'User suspended successfully'
        ]);
    }
    public function verify($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'Active';
        $user->user_type = 'TASK FORCE';
        $user->save();

        return response()->json([
            'message' => 'User verified successfully'
        ]);
    }
}
