@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">Moderate Comments</h1>
                    <p class="text-gray-600">Review and manage user comments</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mt-2">
                        Moderator Feature
                    </span>
                </div>
                <a href="{{ route('dashboard') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-8 text-center">
            <svg class="w-24 h-24 text-blue-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Comment Moderation</h2>
            <p class="text-gray-600 mb-4">This feature is available to moderators and admins.</p>
            <p class="text-sm text-gray-500">You can approve, reject, or delete user comments from here.</p>
            
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-blue-600">0</p>
                    <p class="text-sm text-gray-600">Pending</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-green-600">0</p>
                    <p class="text-sm text-gray-600">Approved</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-red-600">0</p>
                    <p class="text-sm text-gray-600">Rejected</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
