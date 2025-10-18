<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use App\Helpers\ActionMessageHelper;

class UserAccessController extends Controller
{
    public function index()
    {
        $users = User::with('access')->get();
        return view('admin.users.index', compact('users'));
    }

    public function show($userId)
    {
        $user = User::with('access')->findOrFail($userId);
        return view('admin.users.show', compact('user'));
    }

    public function edit($userId)
    {
        $user = User::with('access')->findOrFail($userId);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        $access = $user->access ?: new UserAccess(['user_id' => $userId]);
        
        $access->fill([
            'practice' => $request->has('practice'),
            'exams' => $request->has('exams'),
            'results' => $request->has('results'),
            'classmate' => $request->has('classmate'),
            'leaderboard' => $request->has('leaderboard'),
            'post' => $request->has('post'),
            'read_access' => $request->has('read_access'),
            'mcq_management' => $request->has('mcq_management'),
        ]);
        
        $access->save();
        
        ActionMessageHelper::flash('user_access_updated');
        return redirect()->route('admin.user-access.index');
    }

    public function bulkUpdate(Request $request)
    {
        // Bulk update functionality
        ActionMessageHelper::flash('operation_success');
        return redirect()->route('admin.user-access.index');
    }

    public function reset($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->access) {
            $user->access->delete();
        }
        ActionMessageHelper::flash('user_access_reset');
        return redirect()->route('admin.user-access.index');
    }

    public function grantAll($userId)
    {
        $user = User::findOrFail($userId);
        $access = $user->access ?: new UserAccess(['user_id' => $userId]);
        
        $access->fill([
            'practice' => true,
            'exams' => true,
            'results' => true,
            'classmate' => true,
            'leaderboard' => true,
            'post' => true,
            'read_access' => true,
            'mcq_management' => true,
        ]);
        
        $access->save();
        
        ActionMessageHelper::flash('user_access_granted');
        return redirect()->route('admin.user-access.index');
    }
}