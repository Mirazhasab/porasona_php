@extends('layouts.mcq')

@section('title', 'User Access Management')
@section('page-title', 'User Access Management')
@section('page-subtitle', 'Manage user permissions and access levels')

@section('content')
<div class="p-6">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="p-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left py-3 px-4 font-semibold text-gray-700">User</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Email</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Permissions</th>
              <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr class="border-b border-gray-100 hover:bg-gray-50">
              <td class="py-3 px-4 font-medium text-gray-900">{{ $user->name }}</td>
              <td class="py-3 px-4 text-gray-700">{{ $user->email }}</td>
              <td class="py-3 px-4">
                @php
                  $access = $user->access;
                  $permissions = [];
                  if ($access) {
                    if ($access->practice) $permissions[] = 'Practice';
                    if ($access->exams) $permissions[] = 'Exams';
                    if ($access->results) $permissions[] = 'Results';
                    if ($access->classmate) $permissions[] = 'Classmate';
                    if ($access->leaderboard) $permissions[] = 'Leaderboard';
                    if ($access->post) $permissions[] = 'Posts';
                    if ($access->read_access) $permissions[] = 'Read';
                    if ($access->mcq_management) $permissions[] = 'MCQ Mgmt';
                  }
                @endphp
                <div class="flex flex-wrap gap-1">
                  @foreach($permissions as $permission)
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $permission }}</span>
                  @endforeach
                  @if(empty($permissions))
                    <span class="text-gray-500 text-sm">No permissions</span>
                  @endif
                </div>
              </td>
              <td class="py-3 px-4 text-right">
                <div class="flex space-x-2 justify-end">
                  <a href="{{ route('admin.user-access.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Edit
                  </a>
                  <form action="{{ route('admin.user-access.grant-all', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                      Grant All
                    </button>
                  </form>
                  <form action="{{ route('admin.user-access.reset', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium" onclick="return confirm('Reset all permissions?')">
                      Reset
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection