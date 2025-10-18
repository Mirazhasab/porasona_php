@extends('layouts.admin')

@php
    use App\Models\McqNote;
@endphp

@section('title', 'MCQ Notes Moderation')

@section('content')
<div class="max-w-6xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Question Notes Moderation</h1>
            <p class="text-sm text-gray-500">Approve high-quality explanations, keep private notes private, and guide contributors with constructive feedback.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $status === 'pending' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-100' }}">Pending</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $status === 'approved' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-100' }}">Approved</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $status === 'rejected' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-100' }}">Rejected</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'private']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $status === 'private' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-100' }}">Private</a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $status === 'all' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-100' }}">All</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start gap-2">
            <i class="fas fa-check-circle mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-start gap-2">
            <i class="fas fa-info-circle mt-0.5"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($notes as $note)
            <article class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-gray-500">Question</p>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $note->question->question }}
                        </h2>
                        <div class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-2">
                            <span>Set: {{ $note->question->mcqSet->title }}</span>
                            <span>Question ID: {{ $note->question->id }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Submitted by</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $note->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $note->created_at->diffForHumans() }}</p>
                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full mt-2
                            @class([
                                'bg-green-100 text-green-700' => $note->visibility === McqNote::VISIBILITY_APPROVED,
                                'bg-blue-100 text-blue-700' => $note->visibility === McqNote::VISIBILITY_PENDING,
                                'bg-gray-100 text-gray-600' => $note->visibility === McqNote::VISIBILITY_PRIVATE,
                                'bg-red-100 text-red-700' => $note->visibility === McqNote::VISIBILITY_REJECTED,
                            ])
                        >
                            {{ $note->status_label }}
                        </span>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    @if($note->title)
                        <h3 class="text-sm font-semibold text-gray-900">{{ $note->title }}</h3>
                    @endif
                    <div class="prose prose-sm max-w-none text-gray-800">
                        {!! $note->content_html !!}
                    </div>
                    @if($note->admin_feedback)
                        <div class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            <strong>Previous feedback:</strong> {{ $note->admin_feedback }}
                        </div>
                    @endif

                    <form action="{{ route('admin.notes.approve', $note) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <label class="block text-xs font-semibold text-gray-600">Feedback to author (optional)</label>
                        <textarea name="feedback" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Provide clarification requests, praise, or improvement suggestions..."></textarea>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check"></i>
                                Approve & Publish
                            </button>
                        </div>
                    </form>

                    <form action="{{ route('admin.notes.reject', $note) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <textarea name="feedback" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Reason for rejection (shared with author)"></textarea>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs">
                            <i class="fas fa-times"></i>
                            Reject Note
                        </button>
                    </form>

                    <form action="{{ route('admin.notes.make-private', $note) }}" method="POST" class="inline-flex mt-3">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-xs">
                            <i class="fas fa-lock"></i>
                            Mark Private
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 text-center">
                <i class="fas fa-notes-medical text-3xl text-gray-300"></i>
                <h3 class="mt-3 text-lg font-semibold text-gray-900">No notes found</h3>
                <p class="text-sm text-gray-500">Switch the filter above or encourage learners to submit their study notes.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notes->links() }}
    </div>
</div>
@endsection
