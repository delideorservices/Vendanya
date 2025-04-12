<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\ChatSession;
use App\Services\AIOrchestrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeworkController extends Controller
{
    protected $aiService;

    public function __construct(AIOrchestrationService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $homeworks = Auth::user()->homeworks()->latest()->paginate(10);
        
        return response()->json([
            'homeworks' => $homeworks
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'question' => 'required|string|max:500',
            'content' => 'required|string',
        ]);

        $homework = Auth::user()->homeworks()->create([
            'subject' => $validated['subject'],
            'question' => $validated['question'],
            'content' => $validated['content'],
            'status' => 'pending',
            'metadata' => [
                'source' => $request->input('source', 'web'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        // Create a new chat session for this homework
        $chatSession = ChatSession::create([
            'user_id' => Auth::id(),
            'homework_id' => $homework->id,
            'status' => 'active',
            'context' => [
                'subject' => $validated['subject'],
                'question' => $validated['question'],
            ],
        ]);

        // Initialize AI processing for this homework
        $this->aiService->initializeSession($chatSession);

        return response()->json([
            'message' => 'Homework submitted successfully',
            'homework' => $homework,
            'chat_session_id' => $chatSession->id
        ], 201);
    }

    public function show($id)
    {
        $homework = Auth::user()->homeworks()->findOrFail($id);
        $chatSessions = $homework->chatSessions;
        
        return response()->json([
            'homework' => $homework,
            'chat_sessions' => $chatSessions
        ]);
    }

    public function update(Request $request, $id)
    {
        $homework = Auth::user()->homeworks()->findOrFail($id);
        
        $validated = $request->validate([
            'subject' => 'sometimes|string|max:100',
            'question' => 'sometimes|string|max:500',
            'content' => 'sometimes|string',
        ]);

        $homework->update($validated);
        
        return response()->json([
            'message' => 'Homework updated successfully',
            'homework' => $homework
        ]);
    }

    public function destroy($id)
    {
        $homework = Auth::user()->homeworks()->findOrFail($id);
        $homework->delete();
        
        return response()->json([
            'message' => 'Homework deleted successfully'
        ]);
    }
}