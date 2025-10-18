@extends('layouts.mcq')

@section('title', 'Edit User Access')
@section('page-title', 'Edit User Access')
@section('page-subtitle', 'Manage permissions for {{ $user->name }}')

@section('content')
<div class="p-6">
  <div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <form action="{{ route('admin.user-access.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">User Information</h3>
          <p class="text-gray-600"><strong>Name:</strong> {{ $user->name }}</p>
          <p class="text-gray-600"><strong>Email:</strong> {{ $user->email }}</p>
        </div>
        
        <div class="mb-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Permissions</h3>
          <div class="grid grid-cols-2 gap-4">
            @php $access = $user->access; @endphp
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="practice" {{ $access && $access->practice ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">Practice Questions</span>
            </label>
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="exams" {{ $access && $access->exams ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">Examinations</span>
            </label>
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="results" {{ $access && $access->results ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">Results & Analytics</span>
            </label>
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="classmate" {{ $access && $access->classmate ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">Classmates</span>
            </label>
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="leaderboard" {{ $access && $access->leaderboard ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">Leaderboard</span>
            </label>
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="post" {{ $access && $access->post ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">Posts</span>
            </label>
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="read_access" {{ $access && $access->read_access ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">Read MCQ</span>
            </label>
            
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="mcq_management" {{ $access && $access->mcq_management ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
              <span class="text-sm text-gray-700">MCQ Management</span>
            </label>
          </div>
        </div>
        
        <div class="flex space-x-3">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Update Access
          </button>
          <a href="{{ route('admin.user-access.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection