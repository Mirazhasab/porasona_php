<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\McqNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class McqNoteApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', McqNote::VISIBILITY_PENDING);

        $notesQuery = McqNote::query()
            ->with(['user', 'question.mcqSet'])
            ->latest();

        if ($status === 'all') {
            // no filter
        } elseif ($status === 'approved') {
            $notesQuery->where('visibility', McqNote::VISIBILITY_APPROVED);
        } elseif ($status === 'rejected') {
            $notesQuery->where('visibility', McqNote::VISIBILITY_REJECTED);
        } elseif ($status === 'private') {
            $notesQuery->where('visibility', McqNote::VISIBILITY_PRIVATE);
        } else {
            $notesQuery->where('visibility', McqNote::VISIBILITY_PENDING);
        }

        $notes = $notesQuery->paginate(20)->appends(['status' => $status]);

        return view('admin.notes.index', compact('notes', 'status'));
    }

    public function approve(Request $request, McqNote $note): RedirectResponse
    {
        $request->validate([
            'feedback' => ['nullable', 'string', 'max:500'],
        ]);

        $note->markAsApproved($request->input('feedback'));

        return back()->with('success', 'Note approved and published for all learners.');
    }

    public function reject(Request $request, McqNote $note): RedirectResponse
    {
        $request->validate([
            'feedback' => ['nullable', 'string', 'max:500'],
        ]);

        $note->markAsRejected($request->input('feedback'));

        return back()->with('success', 'Note rejected and the author has been notified.');
    }

    public function makePrivate(McqNote $note): RedirectResponse
    {
        $note->makePrivate();

        return back()->with('info', 'Note reverted to private visibility.');
    }
}
