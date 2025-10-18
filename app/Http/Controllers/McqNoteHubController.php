<?php

namespace App\Http\Controllers;

use App\Models\McqNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class McqNoteHubController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $personalNotes = $user->mcqNotes()
            ->with(['question.mcqSet'])
            ->latest('updated_at')
            ->paginate(6, ['*'], 'personal_page');

        $communityNotes = McqNote::approved()
            ->with(['question.mcqSet', 'user'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->orWhereNull('user_id');
            })
            ->latest('approved_at')
            ->paginate(6, ['*'], 'community_page');

        $pendingNotes = $user->mcqNotes()
            ->pending()
            ->with(['question.mcqSet'])
            ->latest('updated_at')
            ->get();

        $rejectedNotes = $user->mcqNotes()
            ->where('visibility', McqNote::VISIBILITY_REJECTED)
            ->with(['question.mcqSet'])
            ->latest('updated_at')
            ->get();

        return view('mcq.notes.index', [
            'user' => $user,
            'personalNotes' => $personalNotes,
            'communityNotes' => $communityNotes,
            'pendingNotes' => $pendingNotes,
            'rejectedNotes' => $rejectedNotes,
        ]);
    }
}
