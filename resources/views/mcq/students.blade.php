@extends('layouts.mcq')

@section('title', 'Students - MCQ System')
@section('page-title', $isAdmin ? 'Students' : 'Classmates')
@section('page-subtitle', $isAdmin ? 'Manage student accounts' : 'View your classmates')

@section('content')
<div class="flex justify-between items-center mb-6">
  <div>
    <h3 class="text-xl font-semibold text-gray-800">{{ $isAdmin ? 'Student Management' : 'Classmates' }}</h3>
    <p class="text-gray-600">{{ $isAdmin ? 'Manage student accounts and performance' : 'View classmate performance' }}</p>
  </div>
  @if($isAdmin)
  <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition-colors">
    <i class="fas fa-user-plus mr-2"></i>Add Student
  </button>
  @endif
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
  <div class="p-6">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="text-left py-3 px-4 font-medium text-gray-600">Student</th>
            <th class="text-left py-3 px-4 font-medium text-gray-600">Email</th>
            <th class="text-left py-3 px-4 font-medium text-gray-600">Exams Taken</th>
            <th class="text-left py-3 px-4 font-medium text-gray-600">Avg Score</th>
            <th class="text-left py-3 px-4 font-medium text-gray-600">Status</th>
            <th class="text-left py-3 px-4 font-medium text-gray-600">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $student)
          <tr class="border-b border-gray-100">
            <td class="py-3 px-4">
              <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 font-medium text-sm">{{ $student['initials'] }}</span>
                </div>
                <span class="font-medium">{{ $student['name'] }}</span>
              </div>
            </td>
            <td class="py-3 px-4 text-gray-600">{{ $student['email'] }}</td>
            <td class="py-3 px-4">{{ $student['exams_taken'] }}</td>
            <td class="py-3 px-4">{{ $student['avg_score'] }}</td>
            <td class="py-3 px-4">
              <span class="px-2 py-1 rounded-full text-xs
                @if($student['status'] === 'Active') bg-green-100 text-green-800
                @else bg-yellow-100 text-yellow-800
                @endif">
                {{ $student['status'] }}
              </span>
            </td>
            <td class="py-3 px-4">
              @if($isAdmin)
              <button class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-eye"></i></button>
              <button class="text-gray-600 hover:text-gray-800"><i class="fas fa-edit"></i></button>
              @else
              <button class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i> View</button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
