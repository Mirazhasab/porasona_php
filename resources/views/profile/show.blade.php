@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">My Profile</h1>
                    <p class="text-gray-600">Manage your account settings</p>
                </div>
                <a href="{{ route('dashboard') }}" 
                   class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-semibold">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-8">
            <div class="flex items-center space-x-6 mb-8">
                @if(auth()->user()->avatar)
                <img class="h-24 w-24 rounded-full" src="{{ auth()->user()->avatar }}" alt="">
                @else
                <div class="h-24 w-24 rounded-full bg-purple-100 flex items-center justify-center">
                    <span class="text-purple-600 font-semibold text-3xl">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                @endif
                
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-600">{{ auth()->user()->email }}</p>
                    <div class="flex gap-2 mt-2">
                        @foreach(auth()->user()->roles as $role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($role->name === 'admin') bg-red-100 text-red-800
                            @elseif($role->name === 'moderator') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($role->name) }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">My Permissions</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach(auth()->user()->getAllPermissions() as $permission)
                    <div class="flex items-center space-x-2 text-sm">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">{{ $permission->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @can('edit own profile')
            <div class="mt-8">
                <button class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-semibold">
                    Edit Profile
                </button>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
