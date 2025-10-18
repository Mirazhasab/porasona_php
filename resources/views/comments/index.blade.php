@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-teal-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">Comments</h1>
                    <p class="text-gray-600">View and manage comments</p>
                </div>
                <a href="{{ route('dashboard') }}" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-8 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Comments Feature</h2>
            <p class="text-gray-600 mb-4">This feature is available to all users.</p>
            <p class="text-sm text-gray-500">Your comments and discussions will appear here.</p>
        </div>
    </div>
</div>
@endsection
