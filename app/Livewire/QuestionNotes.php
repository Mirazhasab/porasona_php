<?php

namespace App\Livewire;

use App\Models\McqNote;
use App\Models\McqQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class QuestionNotes extends Component
{
    public McqQuestion $question;

    public string $content = '';
    public ?string $title = null;
    public bool $shareWithCommunity = false;
    public ?int $editingNoteId = null;
    public bool $showForm = false;
    public string $activeTab = 'private';
    public ?int $openMenuId = null;

    protected $listeners = [
        'open-note-form' => 'handleOpenNoteForm',
        'close-note-form' => 'handleCloseNoteForm',
        'close-note-menu' => 'closeMenu',
    ];

    public function mount(int $questionId): void
    {
        $this->question = McqQuestion::with('mcqSet')->findOrFail($questionId);
    }

    public function getCommunityNotesProperty()
    {
        return $this->question->notes()
            ->approved()
            ->with(['user'])
            ->latest('approved_at')
            ->get();
    }

    public function getMyNotesProperty()
    {
        if (!Auth::check()) {
            return collect();
        }

        return $this->question->notes()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    protected function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:120'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
            'shareWithCommunity' => ['boolean'],
        ];
    }

    public function edit(int $noteId): void
    {
        $this->authorizeUser();
        $note = $this->findOwnedNote($noteId);
        $this->editingNoteId = $note->id;
        $this->title = $note->title;
        $this->content = $note->content;
        $this->shareWithCommunity = in_array($note->visibility, [
            McqNote::VISIBILITY_PENDING,
            McqNote::VISIBILITY_APPROVED,
        ], true);
        $this->showForm = true;
        $this->activeTab = 'private';
        $this->openMenuId = null;

        $this->dispatch('notes-open-form', questionId: $this->question->id);
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingNoteId', 'title', 'content', 'shareWithCommunity', 'showForm']);
        $this->activeTab = 'private';
        $this->openMenuId = null;
        $this->dispatch('notes-form-reset', questionId: $this->question->id);
    }

    public function saveNote(): void
    {
        $this->authorizeUser();

        $data = $this->validate();
        $shareRequested = (bool) ($data['shareWithCommunity'] ?? false);

        if ($this->editingNoteId) {
            $note = $this->findOwnedNote($this->editingNoteId);
            $note->fill([
                'title' => $data['title'] ?? null,
                'content' => $data['content'],
            ]);

            if ($shareRequested) {
                $note->requestShare();
            } else {
                $note->makePrivate();
            }

            $note->save();
        } else {
            $note = new McqNote([
                'mcq_question_id' => $this->question->id,
                'user_id' => Auth::id(),
                'title' => $data['title'] ?? null,
                'content' => $data['content'],
                'share_requested' => $shareRequested,
                'visibility' => $shareRequested ? McqNote::VISIBILITY_PENDING : McqNote::VISIBILITY_PRIVATE,
            ]);

            $note->save();
        }

        if ($shareRequested) {
            $this->dispatch('notes-toast', type: 'info', message: 'Note submitted for admin review. You will be notified once it is approved.');
        } else {
            $this->dispatch('notes-toast', type: 'success', message: 'Private note saved successfully.');
        }

        $this->reset(['editingNoteId', 'title', 'content', 'shareWithCommunity', 'showForm']);
        $this->activeTab = 'private';
        $this->openMenuId = null;
        $this->dispatch('notes-updated');
        $this->dispatch('notes-form-reset', questionId: $this->question->id);
    }

    public function deleteNote(int $noteId): void
    {
        $this->authorizeUser();
        $note = $this->findOwnedNote($noteId);
        $note->delete();

        $this->openMenuId = null;

        $this->dispatch('notes-toast', type: 'success', message: 'Note deleted.');
        $this->dispatch('notes-updated');
        $this->activeTab = 'private';
    }

    public function requestApproval(int $noteId): void
    {
        $this->authorizeUser();
        $note = $this->findOwnedNote($noteId);
        $note->requestShare();

        $this->openMenuId = null;

        $this->dispatch('notes-toast', type: 'info', message: 'Note sent for admin approval.');
        $this->dispatch('notes-updated');
        $this->activeTab = 'private';
    }

    public function makePrivate(int $noteId): void
    {
        $this->authorizeUser();
        $note = $this->findOwnedNote($noteId);
        $note->makePrivate();

        $this->openMenuId = null;

        $this->dispatch('notes-toast', type: 'success', message: 'Note is now private.');
        $this->dispatch('notes-updated');
        $this->activeTab = 'private';
    }

    protected function findOwnedNote(int $noteId): McqNote
    {
        $note = $this->question->notes()->where('id', $noteId)->where('user_id', Auth::id())->first();

        if (!$note) {
            throw ValidationException::withMessages([
                'note' => 'Unable to locate the requested note.',
            ]);
        }

        return $note;
    }

    protected function authorizeUser(): void
    {
        if (!Auth::check()) {
            throw ValidationException::withMessages([
                'auth' => 'You must be logged in to create notes.',
            ]);
        }
    }

    public function handleOpenNoteForm(int $questionId): void
    {
        if ($this->question->id !== $questionId) {
            return;
        }

        if (!Auth::check()) {
            $this->dispatch('notes-toast', type: 'warning', message: 'Log in to start a private note.');
            return;
        }

        $this->showForm = true;
        $this->activeTab = 'private';
        $this->openMenuId = null;
        $this->dispatch('notes-open-form', questionId: $this->question->id);
    }

    public function handleCloseNoteForm(int $questionId): void
    {
        if ($this->question->id !== $questionId) {
            return;
        }

        $this->showForm = false;
        $this->openMenuId = null;
        $this->dispatch('notes-form-reset', questionId: $this->question->id);
    }

    public function switchTab(string $tab): void
    {
        if (!in_array($tab, ['private', 'public'], true)) {
            return;
        }

        \Log::debug('question-notes switchTab', ['tab' => $tab, 'question' => $this->question->id, 'user' => Auth::id()]);

        $this->activeTab = $tab;
        $this->openMenuId = null;

        if ($tab === 'public') {
            $this->showForm = false;
            $this->dispatch('notes-form-reset', questionId: $this->question->id);
        }
    }

    public function startNewNote(): void
    {
        if (!Auth::check()) {
            $this->dispatch('notes-toast', type: 'warning', message: 'Please log in to add a note.');
            return;
        }

        $this->reset(['editingNoteId', 'title', 'content', 'shareWithCommunity']);
        $this->showForm = true;
        $this->activeTab = 'private';
        $this->openMenuId = null;

        $this->dispatch('notes-open-form', questionId: $this->question->id);
    }

    public function toggleMenu(int $noteId): void
    {
        $this->openMenuId = $this->openMenuId === $noteId ? null : $noteId;
    }

    public function closeMenu(): void
    {
        $this->openMenuId = null;
    }

    public function render()
    {
        return view('livewire.question-notes');
    }
}
