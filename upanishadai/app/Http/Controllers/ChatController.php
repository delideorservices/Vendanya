<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use App\Events\AgentTyping;
use App\Services\AIOrchestrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $aiService;

    public function __construct(AIOrchestrationService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index($sessionId)
    {
        $chatSession = ChatSession::with('messages.sender')
            ->where('user_id', Auth::id())
            ->findOrFail($sessionId);
        
        return response()->json([
            'session' => $chatSession,
            'messages' => $chatSession->messages
        ]);
    }

    public function sendMessage(Request $request, $sessionId)
    {
        $chatSession = ChatSession::where('user_id', Auth::id())
            ->findOrFail($sessionId);
        
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        // Create the user message
        $message = ChatMessage::create([
            'chat_session_id' => $chatSession->id,
            'sender_type' => 'App\\Models\\User',
            'sender_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        // Broadcast the message to the session channel
        broadcast(new MessageSent($message))->toOthers();

        // Indicate that agent is typing
        broadcast(new AgentTyping($chatSession->id))->toOthers();

        // Process the message with AI
        $this->aiService->processUserMessage($chatSession, $message);
        
        return response()->json([
            'message' => 'Message sent successfully',
            'chat_message' => $message
        ]);
    }

    public function getMessages($sessionId)
    {
        $chatSession = ChatSession::where('user_id', Auth::id())
            ->findOrFail($sessionId);
        
        $messages = $chatSession->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();
        
        return response()->json([
            'messages' => $messages
        ]);
    }

    public function createSession(Request $request)
    {
        $validated = $request->validate([
            'homework_id' => 'sometimes|exists:homeworks,id',
            'agent_id' => 'sometimes|exists:agents,id',
        ]);

        $chatSession = ChatSession::create([
            'user_id' => Auth::id(),
            'homework_id' => $request->input('homework_id'),
            'agent_id' => $request->input('agent_id'),
            'status' => 'active',
        ]);

        // Initialize the AI session
        $this->aiService->initializeSession($chatSession);

        return response()->json([
            'message' => 'Chat session created successfully',
            'chat_session' => $chatSession
        ], 201);
    }

    public function endSession($sessionId)
    {
        $chatSession = ChatSession::where('user_id', Auth::id())
            ->findOrFail($sessionId);
        
        $chatSession->update(['status' => 'closed']);
        
        return response()->json([
            'message' => 'Chat session ended successfully'
        ]);
    }
}