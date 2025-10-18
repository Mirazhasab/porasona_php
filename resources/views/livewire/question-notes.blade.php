@php
	use App\Models\McqNote;
@endphp

@once
	@push('styles')
		<style>
			.note-highlight {
				padding: 0 0.25em;
				border-radius: 0.35rem;
				font-weight: 500;
			}

			.note-highlight-sun { background-color: #fef3c7; color: #92400e; }
			.note-highlight-mint { background-color: #d1fae5; color: #065f46; }
			.note-highlight-sky { background-color: #dbeafe; color: #1d4ed8; }
			.note-highlight-rose { background-color: #ffe4e6; color: #9d174d; }
			.note-highlight-violet { background-color: #ede9fe; color: #5b21b6; }

			.note-highlight-swatch {
				display: inline-flex;
				width: 1.5rem;
				height: 1.5rem;
				border-radius: 9999px;
				border: 1px solid rgba(15, 23, 42, 0.15);
			}

			.note-card-menu {
				z-index: 30;
				min-width: 12rem;
			}
		</style>
	@endpush
@endonce

<div class="relative space-y-4" data-question-notes data-question-id="{{ $question->id }}">
	@if($openMenuId)
		<button type="button" wire:click="closeMenu" class="fixed inset-0 z-20 block h-full w-full cursor-default bg-transparent" aria-label="Close note menu"></button>
	@endif

	<section class="rounded-xl border border-gray-200 bg-white shadow-sm">
		<header class="flex flex-col gap-4 border-b border-gray-100 p-4 md:flex-row md:items-center md:justify-between">
			<div class="flex items-center gap-2">
				<i data-lucide="notebook" class="h-5 w-5 text-blue-600"></i>
				<div>
					<h2 class="text-base font-semibold text-gray-900">Question Notes Workspace</h2>
					<p class="text-xs text-gray-500">Toggle between private drafts and community explanations.</p>
				</div>
			</div>
			<nav class="flex items-center gap-2 rounded-lg bg-gray-100 p-1" aria-label="Notes tabs">
				<button type="button" wire:click="switchTab('private')" data-notes-tab="private" @class([
						'inline-flex items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition-colors',
						'bg-white text-blue-700 shadow-sm' => $activeTab === 'private',
						'text-gray-600 hover:text-gray-800' => $activeTab !== 'private',
				])>
					<span>Private</span>
					<span class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-current text-[11px]">{{ $this->myNotes->count() }}</span>
				</button>
				<button type="button" wire:click="switchTab('public')" data-notes-tab="public" @class([
						'inline-flex items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold transition-colors',
						'bg-white text-blue-700 shadow-sm' => $activeTab === 'public',
						'text-gray-600 hover:text-gray-800' => $activeTab !== 'public',
				])>
					<span>Community</span>
					<span class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-current text-[11px]">{{ $this->communityNotes->count() }}</span>
				</button>
			</nav>
		</header>

		<div class="space-y-4 p-4">
			@if($activeTab === 'private')
				@auth
					<div class="flex flex-wrap items-center justify-between gap-3">
						<p class="text-sm text-gray-600">Draft personal strategies, mnemonics, or reminders. Submit when you want moderators to review.</p>
						<div class="flex items-center gap-2">
							<button type="button" wire:click="startNewNote" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
								<i data-lucide="plus" class="h-3 w-3"></i>
								{{ $editingNoteId ? 'Continue Editing' : 'New Private Note' }}
							</button>
							<button type="button" wire:click="$refresh" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
								<i data-lucide="refresh-cw" class="h-3 w-3"></i>
								Refresh
							</button>
						</div>
					</div>
				@else
					<div class="rounded-lg border border-dashed border-blue-300 bg-blue-50 p-4 text-sm text-blue-800">
						<strong>Login required:</strong> sign in to keep private notes and request community sharing.
					</div>
				@endauth

				<div @class([
						'note-form-card rounded-lg border border-gray-200 bg-white shadow-sm',
						'hidden' => !$showForm,
				]) data-question-id="{{ $question->id }}">
					<div class="flex items-start justify-between gap-3 border-b border-gray-100 p-4">
						<div>
							<h3 class="text-sm font-semibold text-gray-900">{{ $editingNoteId ? 'Edit Private Note' : 'Create Private Note' }}</h3>
							<p class="text-xs text-gray-500">Supports Markdown, MathJax, and code blocks for deep explanations.</p>
						</div>
						@if($editingNoteId || $showForm)
							<button type="button" wire:click="cancelEdit" class="text-xs text-gray-500 hover:text-gray-700">Close</button>
						@endif
					</div>
					<div class="p-4">
						@auth
							<form wire:submit.prevent="saveNote" class="space-y-5" data-note-highlight-form>
								<div class="space-y-2">
									<label for="note-title" class="text-xs font-semibold text-gray-600">Title (optional)</label>
									<input type="text" name="title" id="note-title" wire:model.defer="title" maxlength="120" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200" placeholder="e.g. Alternate elimination strategy">
									@error('title')
										<p class="text-xs text-red-500">{{ $message }}</p>
									@enderror
								</div>

								<div class="space-y-2">
									<label for="note-content" class="flex items-center justify-between text-xs font-semibold text-gray-600">
										<span>Detailed notes</span>
										<a href="https://www.mathjax.org/#samples" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-700">Math syntax guide</a>
									</label>
									<textarea name="content" id="note-content" wire:model.defer="content" rows="6" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200" placeholder="Explain the reasoning, highlight traps, or document alternative methods..."></textarea>
									@error('content')
										<p class="text-xs text-red-500">{{ $message }}</p>
									@enderror
								</div>

								<div class="space-y-2">
									<div class="flex items-center justify-between text-xs font-semibold text-gray-600">
										<span>Highlight colors</span>
										<span class="text-[11px] font-normal text-gray-500">Select text first, then apply a color.</span>
									</div>
									<div class="flex flex-wrap gap-2">
										<button type="button" data-highlight-color="sun" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-amber-300 hover:bg-amber-50">
											<span class="note-highlight-swatch note-highlight-sun"></span>
											Sunray
										</button>
										<button type="button" data-highlight-color="mint" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-emerald-300 hover:bg-emerald-50">
											<span class="note-highlight-swatch note-highlight-mint"></span>
											Fresh Mint
										</button>
										<button type="button" data-highlight-color="sky" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-blue-300 hover:bg-blue-50">
											<span class="note-highlight-swatch note-highlight-sky"></span>
											Sky Blue
										</button>
										<button type="button" data-highlight-color="rose" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-rose-300 hover:bg-rose-50">
											<span class="note-highlight-swatch note-highlight-rose"></span>
											Rose Accent
										</button>
										<button type="button" data-highlight-color="violet" class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-violet-300 hover:bg-violet-50">
											<span class="note-highlight-swatch note-highlight-violet"></span>
											Soft Violet
										</button>
									</div>
									<p class="text-[11px] text-gray-500">Remove a highlight by deleting the surrounding [[hl:color]] and [[/hl]] tags.</p>
								</div>

								<div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
									<label for="note-share" class="inline-flex items-center gap-2">
										<input type="checkbox" id="note-share" name="share_with_community" wire:model.defer="shareWithCommunity" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
										<span>Request publication after moderator approval</span>
									</label>
									<span>Markdown + MathJax supported</span>
								</div>

								<div class="flex flex-wrap items-center gap-2">
									<button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700" wire:loading.attr="disabled">
										<span wire:loading.remove wire:target="saveNote">{{ $editingNoteId ? 'Update Note' : 'Save Private Note' }}</span>
										<span wire:loading wire:target="saveNote" class="inline-flex items-center gap-2">
											<i class="fas fa-spinner fa-spin"></i>
											Saving...
										</span>
									</button>
									@if($editingNoteId)
										<button type="button" wire:click="cancelEdit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100">Cancel</button>
									@endif
								</div>
							</form>
						@else
							<p class="text-sm text-gray-500">Log in to create personal notes and request moderator review.</p>
						@endauth
					</div>
				</div>

				<div class="space-y-4">
					@forelse($this->myNotes as $note)
						<article class="rounded-2xl border border-gray-200 bg-white shadow-sm" wire:key="note-{{ $note->id }}">
							<header class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 bg-gray-50 px-6 py-3">
								<div class="flex items-center gap-2">
									<span class="inline-flex items-center justify-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide @class([
										'bg-green-100 text-green-700 border border-green-200' => $note->visibility === McqNote::VISIBILITY_APPROVED,
										'bg-yellow-100 text-yellow-700 border border-yellow-200' => $note->visibility === McqNote::VISIBILITY_PENDING,
										'bg-red-100 text-red-700 border border-red-200' => $note->visibility === McqNote::VISIBILITY_REJECTED,
										'bg-gray-100 text-gray-600 border border-gray-200' => $note->visibility === McqNote::VISIBILITY_PRIVATE,
									])">{{ $note->status_label }}</span>
									@if($note->title)
										<span class="text-sm font-semibold text-gray-900">{{ $note->title }}</span>
									@endif
								</div>
								<div class="flex items-center gap-3">
									<span class="text-xs text-gray-500">Updated {{ $note->updated_at->diffForHumans() }}</span>
									<div class="relative z-30">
										<button type="button" wire:click="toggleMenu({{ $note->id }})" class="note-menu-trigger inline-flex items-center justify-center rounded-full border border-gray-300 bg-white p-2 text-gray-600 hover:bg-gray-100" aria-label="Open note actions">
											<i data-lucide="more-vertical" class="h-4 w-4"></i>
										</button>
										@if($openMenuId === $note->id)
											<div class="note-card-menu absolute right-0 mt-2 rounded-xl border border-gray-200 bg-white py-2 text-sm shadow-lg">
												<button type="button" wire:click="edit({{ $note->id }})" class="flex w-full items-center gap-2 px-4 py-2 text-gray-700 hover:bg-gray-50">
													<i data-lucide="edit-3" class="h-4 w-4"></i>
													Edit note
												</button>
												<button type="button" wire:click="deleteNote({{ $note->id }})" class="flex w-full items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
													<i data-lucide="trash-2" class="h-4 w-4"></i>
													Delete note
												</button>
												@if(in_array($note->visibility, [McqNote::VISIBILITY_PRIVATE, McqNote::VISIBILITY_REJECTED], true))
													<button type="button" wire:click="requestApproval({{ $note->id }})" class="flex w-full items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-50">
														<i data-lucide="send" class="h-4 w-4"></i>
														Submit for review
													</button>
												@endif
												@if($note->visibility === McqNote::VISIBILITY_PENDING)
													<button type="button" wire:click="makePrivate({{ $note->id }})" class="flex w-full items-center gap-2 px-4 py-2 text-gray-600 hover:bg-gray-50">
														<i data-lucide="shield-off" class="h-4 w-4"></i>
														Keep private
													</button>
												@endif
											</div>
										@endif
									</div>
								</div>
							</header>
							<div class="space-y-4 px-6 py-5">
								<div class="prose prose-sm max-w-none leading-relaxed text-gray-800">
									{!! $note->content_html !!}
								</div>
								@if($note->admin_feedback)
									<div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
										<strong class="font-semibold">Moderator feedback:</strong>
										<span class="ml-1">{{ $note->admin_feedback }}</span>
									</div>
								@endif
							</div>
						</article>
					@empty
						<div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">
							No private notes yet. Start one above to capture your process or quick reminders.
						</div>
					@endforelse
				</div>
			@else
				<div class="flex flex-wrap items-center justify-between gap-3">
					<p class="text-sm text-gray-600">Moderator-approved notes showcase polished explanations from top learners.</p>
					<button type="button" wire:click="$refresh" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
						<i data-lucide="refresh-cw" class="h-3 w-3"></i>
						Refresh
					</button>
				</div>

				<div class="space-y-3">
					@forelse($this->communityNotes as $note)
						<article class="rounded-xl border border-blue-100 bg-white shadow-sm">
							<header class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-100 bg-blue-50 px-4 py-2.5">
								<p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-blue-700">
									<i data-lucide="sparkles" class="h-4 w-4"></i>
									{{ $note->user->name }}
								</p>
								<span class="text-xs text-blue-700">Approved {{ optional($note->approved_at)->diffForHumans() }}</span>
							</header>
							<div class="space-y-3 px-4 py-4">
								@if($note->title)
									<h3 class="text-base font-semibold text-blue-900">{{ $note->title }}</h3>
								@endif
								<div class="prose prose-sm max-w-none leading-relaxed text-blue-900">
									{!! $note->content_html !!}
								</div>
							</div>
						</article>
					@empty
						<div class="rounded-lg border border-dashed border-blue-300 bg-blue-50 p-4 text-sm text-blue-700">
							No community explanations yet. Submit a refined private note for moderator review.
						</div>
					@endforelse
				</div>
			@endif
		</div>
	</section>
</div>

@once
	@push('scripts')
		<script>
			(function loadMathJax() {
				if (!document.getElementById('mathjax-script')) {
					const script = document.createElement('script');
					script.id = 'mathjax-script';
					script.type = 'text/javascript';
					script.src = 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js';
					script.async = true;
					document.head.appendChild(script);
				}
			})();

			function renderNotesMath() {
				if (window.MathJax && window.MathJax.typesetPromise) {
					window.MathJax.typesetPromise();
				}
				if (window.lucide && window.lucide.createIcons) {
					window.lucide.createIcons();
				}
			}

			document.addEventListener('DOMContentLoaded', renderNotesMath);
			document.addEventListener('livewire:navigated', renderNotesMath);
			window.addEventListener('notes-updated', renderNotesMath);

			window.addEventListener('notes-toast', function (event) {
				const detail = event.detail || {};
				const message = detail.message || 'Action completed';
				const type = detail.type || 'info';
				if (window.showAlert) {
					window.showAlert(message, type);
				} else {
					console.log((type || 'info').toUpperCase() + ': ' + message);
				}
			});
		</script>
		<script src="{{ asset('js/question-notes.js') }}" defer></script>
	@endpush
@endonce
