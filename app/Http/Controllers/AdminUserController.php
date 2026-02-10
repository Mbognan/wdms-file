<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $loggedInUser = auth()->user();

        $isAdmin = $loggedInUser->user_type === UserType::ADMIN;
        $isDean  = $loggedInUser->user_type === UserType::DEAN;

        $users = User::where('status', 'pending')->get();

        return view('admin.users.index', compact(
            'isAdmin',
            'isDean',
            'users'
        ));
    }


    public function data()
    {
        $viewer = auth()->user();

        $query = User::where('status', UserStatus::PENDING);

        match ($viewer->user_type) {

            UserType::ADMIN => $query->whereIn('user_type', [
                UserType::INTERNAL_ASSESSOR,
                UserType::ACCREDITOR,
            ]),

            UserType::DEAN => $query->where('user_type', UserType::TASK_FORCE),

            default => $query->whereRaw('1 = 0'),
        };

        return response()->json([
            'data' => $query->get(),
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
    public function verify(Request $request, $id)
    {
        $request->validate([
            'user_type' => 'required|string'
        ]);

        $user = User::findOrFail($id);

        $user->status = 'Active';
        $user->user_type = $request->user_type; // assigned by dean
        $user->save();

        return response()->json([
            'message' => 'User verified and role assigned successfully'
        ]);
    }
}
