@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-yellow-50 via-white to-orange-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">Reports</h1>
                    <p class="text-gray-600">View and manage user reports</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mt-2">
                        Moderator Feature
                    </span>
                </div>
                <a href="{{ route('dashboard') }}" 
                   class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 transition-colors font-semibold">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-8 text-center">
            <svg class="w-24 h-24 text-yellow-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Reports & Analytics</h2>
            <p class="text-gray-600 mb-4">This feature is available to moderators and admins.</p>
            <p class="text-sm text-gray-500">View system reports, user issues, and analytics data.</p>
        </div>
    </div>
</div>
@endsection
