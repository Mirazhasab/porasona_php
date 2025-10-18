@extends('layouts.mcq')

@section('title', 'Notes Hub')
@section('page-title', 'Notes Hub')
@section('page-subtitle', 'Capture insights, review feedback, and explore community knowledge')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="space-y-6">
  <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm">
    <div class="p-6 lg:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
      <div class="max-w-2xl">
        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">Your study HQ for every MCQ</h2>
        <p class="mt-3 text-sm lg:text-base text-gray-600 leading-relaxed">
          Track your private notes, monitor pending submissions, and discover peer insights all in one streamlined hub. Publish-ready notes shine here once approved by the academic team.
        </p>
      </div>
      <div class="flex flex-wrap gap-4">
        <a href="{{ route('mcq.read') }}" wire:navigate class="inline-flex items-center px-4 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow hover:bg-blue-700 transition-colors">
          <i data-lucide="file-pen" class="w-4 h-4 mr-2 text-white"></i>
          Start a new note
        </a>
        <a href="{{ route('mcq.dashboard') }}" wire:navigate class="inline-flex items-center px-4 py-3 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
          <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-gray-500"></i>
          Back to dashboard
        </a>
      </div>
    </div>
  </div>

  <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">My latest notes</h3>
            <p class="text-sm text-gray-600">Quick access to the explanations and memory hooks you rely on.</p>
          </div>
          <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 border border-blue-200">
            {{ $personalNotes->total() }} saved
          </span>
        </div>
        <div class="divide-y divide-gray-200">
          @forelse($personalNotes as $note)
            <article class="p-5 hover:bg-gray-50 transition-colors">
              <div class="flex flex-wrap items-center gap-3 justify-between">
                <div>
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $note->question->mcqSet->title ?? 'MCQ Set' }}</p>
                  <h4 class="text-base font-semibold text-gray-900 mt-1">{{ $note->title ?: 'Untitled note' }}</h4>
                </div>
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full border
                  @class([
                    'bg-green-100 text-green-700 border-green-200' => $note->visibility === \App\Models\McqNote::VISIBILITY_APPROVED,
                    'bg-yellow-100 text-yellow-700 border-yellow-200' => $note->visibility === \App\Models\McqNote::VISIBILITY_PENDING,
                    'bg-red-100 text-red-700 border-red-200' => $note->visibility === \App\Models\McqNote::VISIBILITY_REJECTED,
                    'bg-gray-100 text-gray-700 border-gray-200' => $note->visibility === \App\Models\McqNote::VISIBILITY_PRIVATE,
                  ])
                >
                  {{ $note->status_label }}
                </span>
              </div>
              <p class="mt-3 text-sm text-gray-600 line-clamp-3">{{ Str::limit(strip_tags($note->content), 300) }}</p>
              <div class="mt-4 flex flex-wrap items-center justify-between text-xs text-gray-500">
                <span class="flex items-center">
                  <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                  Updated {{ $note->updated_at->diffForHumans() }}
                </span>
                @if($note->question && $note->question->mcqSet)
                <a href="{{ route('mcq.read.content', $note->question->mcqSet) }}#question-{{ $note->question->id }}" wire:navigate class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                  Review question
                  <i data-lucide="arrow-up-right" class="w-3 h-3 ml-1"></i>
                </a>
                @endif
              </div>
            </article>
          @empty
            <div class="p-6 text-center">
              <i data-lucide="notebook" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
              <p class="text-sm text-gray-600">No personal notes yet. Start capturing insights while reviewing MCQs.</p>
            </div>
          @endforelse
        </div>
        @if($personalNotes->hasPages())
          <div class="px-6 py-4 border-t border-gray-200">
            {{ $personalNotes->withQueryString()->links() }}
          </div>
        @endif
      </div>

      <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Community highlights</h3>
            <p class="text-sm text-gray-600">Notes approved by moderators and shared by top performers.</p>
          </div>
          <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
            {{ $communityNotes->total() }} published
          </span>
        </div>
        <div class="divide-y divide-gray-200">
          @forelse($communityNotes as $note)
            <article class="p-5 hover:bg-gray-50 transition-colors">
              <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                  <p class="text-xs font-semibold text-emerald-600 flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    {{ $note->question->mcqSet->title ?? 'MCQ Set' }}
                  </p>
                  <h4 class="text-base font-semibold text-gray-900 mt-1">{{ $note->title ?: 'Community insight' }}</h4>
                  <p class="mt-2 text-xs text-gray-500">Shared by {{ $note->user?->name ?? 'Anonymous learner' }} • {{ optional($note->approved_at)->diffForHumans() }}</p>
                </div>
                @if($note->question && $note->question->mcqSet)
                <a href="{{ route('mcq.read.content', $note->question->mcqSet) }}#question-{{ $note->question->id }}" wire:navigate class="inline-flex items-center text-blue-600 hover:text-blue-700 text-xs font-semibold">
                  View in context
                  <i data-lucide="arrow-up-right" class="w-3 h-3 ml-1"></i>
                </a>
                @endif
              </div>
              <p class="mt-3 text-sm text-gray-600 line-clamp-4">{{ Str::limit(strip_tags($note->content), 360) }}</p>
            </article>
          @empty
            <div class="p-6 text-center">
              <i data-lucide="users" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
              <p class="text-sm text-gray-600">No community notes are live yet. Share your best explanations to help peers once approved.</p>
            </div>
          @endforelse
        </div>
        @if($communityNotes->hasPages())
          <div class="px-6 py-4 border-t border-gray-200">
            {{ $communityNotes->withQueryString()->links() }}
          </div>
        @endif
      </div>
    </div>

    <aside class="space-y-6">
      <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Pending reviews</h3>
          <p class="text-sm text-gray-600">These notes are waiting for the academic team to approve.</p>
        </div>
        <div class="divide-y divide-gray-200">
          @forelse($pendingNotes as $note)
            <div class="p-5">
              <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">Submitted {{ $note->updated_at->diffForHumans() }}</p>
              <h4 class="text-sm font-semibold text-gray-900 mt-1">{{ $note->title ?: 'Pending note' }}</h4>
              <p class="mt-2 text-xs text-gray-600 line-clamp-3">{{ Str::limit(strip_tags($note->content), 220) }}</p>
            </div>
          @empty
            <div class="p-6 text-center">
              <p class="text-sm text-gray-600">No pending notes right now.</p>
            </div>
          @endforelse
        </div>
      </div>

      <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Feedback & retries</h3>
          <p class="text-sm text-gray-600">Review moderator guidance and update your note before resubmitting.</p>
        </div>
        <div class="divide-y divide-gray-200">
          @forelse($rejectedNotes as $note)
            <div class="p-5">
              <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">Rejected {{ $note->updated_at->diffForHumans() }}</p>
              <h4 class="text-sm font-semibold text-gray-900 mt-1">{{ $note->title ?: 'Needs revision' }}</h4>
              <p class="mt-2 text-xs text-gray-600 line-clamp-3">{{ Str::limit(strip_tags($note->content), 220) }}</p>
              @if($note->admin_feedback)
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                  <strong class="block font-semibold mb-1">Moderator feedback</strong>
                  {{ $note->admin_feedback }}
                </div>
              @endif
            </div>
          @empty
            <div class="p-6 text-center">
              <p class="text-sm text-gray-600">No notes need revisions. Great job!</p>
            </div>
          @endforelse
        </div>
      </div>
    </aside>
  </section>
</div>
@endsection
