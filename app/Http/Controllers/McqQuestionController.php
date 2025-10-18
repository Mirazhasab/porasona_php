<?php

namespace App\Http\Controllers;

use App\Models\McqQuestion;
use App\Models\McqSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class McqQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $mcqSetId = $request->get('mcq_set_id');
        $mcqSet = McqSet::findOrFail($mcqSetId);
        
        $user = Auth::user();
        
        // Check if user can add questions to this set (admin can add to any set)
        $isAdmin = $user->hasRole('admin') || $user->role === 'admin';
        if (!$isAdmin && ($mcqSet->user_id !== $user->id || $mcqSet->status === 'approved')) {
            abort(403, 'You cannot add questions to this MCQ set.');
        }

        return view('mcq_management.questions.create', compact('mcqSet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mcq_set_id' => 'required|exists:mcq_sets,id',
                'question' => 'required|string',
                'ans_1' => 'required|string|max:500',
                'ans_2' => 'required|string|max:500',
                'ans_3' => 'required|string|max:500',
                'ans_4' => 'required|string|max:500',
                'correct_ans' => 'required|in:1,2,3,4',
                'notes' => 'nullable|string',
                'marks' => 'required|integer|min:1',
                '_nonce' => 'required|string|size:32',
            ]);

            $nonceKey = 'mcq_question_nonce_' . $validated['_nonce'];
            if (session()->has($nonceKey)) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Duplicate submission detected.'
                    ], 429);
                }
                return redirect()->back()->with('error', 'Duplicate submission detected.');
            }
            session()->put($nonceKey, true);

            $mcqSet = McqSet::findOrFail($validated['mcq_set_id']);
            $user = Auth::user();
            // Authorization check - admin can add to any set
            $isAdmin = $user->hasRole('admin') || $user->role === 'admin';
            if (!$isAdmin && $mcqSet->user_id !== $user->id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only add questions to your own MCQ sets.'
                    ], 403);
                }
                abort(403, 'You can only add questions to your own MCQ sets.');
            }

            // Create the question with explicit error handling
            $question = new McqQuestion();
            $question->mcq_set_id = $validated['mcq_set_id'];
            $question->question = $validated['question'];
            $question->ans_1 = $validated['ans_1'];
            $question->ans_2 = $validated['ans_2'];
            $question->ans_3 = $validated['ans_3'];
            $question->ans_4 = $validated['ans_4'];
            $question->correct_ans = $validated['correct_ans'];
            $question->notes = $validated['notes'] ?? null;
            $question->marks = $validated['marks'];
            if (!$question->save()) {
                throw new \Exception('Failed to save question to database');
            }

            // Remove nonce after use
            session()->forget($nonceKey);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Question saved successfully!',
                    'redirect' => route('mcq_questions.create', ['mcq_set_id' => $mcqSet->id])
                ]);
            }
            
            return redirect()
                ->route('mcq_questions.create', ['mcq_set_id' => $mcqSet->id])
                ->with('success', 'Question saved successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            // Handle any other errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(McqQuestion $mcqQuestion)
    {
        $mcqQuestion->load(['options', 'mcqSet']);
        return view('mcq_management.questions.show', compact('mcqQuestion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(McqQuestion $mcqQuestion)
    {
        $user = Auth::user();
        $mcqSet = $mcqQuestion->mcqSet;
        
        // Check if user can edit this question (admin can edit any question)
        $isAdmin = $user->hasRole('admin') || $user->role === 'admin';
        if (!$isAdmin && ($mcqSet->user_id !== $user->id || $mcqSet->status === 'approved')) {
            abort(403, 'You cannot edit this question.');
        }

        return view('mcq_management.questions.edit', compact('mcqQuestion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, McqQuestion $mcqQuestion)
    {
        $user = Auth::user();
        $mcqSet = $mcqQuestion->mcqSet;
        
        // Check if user can update this question (admin can update any question)
        $isAdmin = $user->hasRole('admin') || $user->role === 'admin';
        if (!$isAdmin && ($mcqSet->user_id !== $user->id || $mcqSet->status === 'approved')) {
            abort(403, 'You cannot update this question.');
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'ans_1' => 'required|string|max:500',
            'ans_2' => 'required|string|max:500',
            'ans_3' => 'required|string|max:500',
            'ans_4' => 'required|string|max:500',
            'correct_ans' => 'required|in:1,2,3,4',
            'notes' => 'nullable|string',
            'marks' => 'required|integer|min:1',
        ]);

        $mcqQuestion->update($validated);

        return redirect()
            ->route('mcq_sets.show', $mcqSet)
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(McqQuestion $mcqQuestion)
    {
        $user = Auth::user();
        $mcqSet = $mcqQuestion->mcqSet;
        
        // Check if user can delete this question (admin can delete any question)
        $isAdmin = $user->hasRole('admin') || $user->role === 'admin';
        if (!$isAdmin && ($mcqSet->user_id !== $user->id || $mcqSet->status === 'approved')) {
            abort(403, 'You cannot delete this question.');
        }

        $mcqQuestion->delete();

        return redirect()
            ->route('mcq_sets.show', $mcqSet)
            ->with('success', 'Question deleted successfully!');
    }
}
