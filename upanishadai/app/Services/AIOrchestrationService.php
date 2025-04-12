<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Agent;
use App\Events\MessageSent;
use App\Events\AgentTyping;
use App\Events\UIComponentTriggered;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AIOrchestrationService
{
    protected $flaskApiUrl;

    public function __construct()
    {
        $this->flaskApiUrl = env('FLASK_API_URL', 'http://flask:5000');
    }

    public function initializeSession(ChatSession $session)
    {
        try {
            // If no agent is assigned, assign a random one
            if (!$session->agent_id) {
                $agent = Agent::inRandomOrder()->first();
                $session->agent_id = $agent->id;
                $session->save();
            }

            // Call Flask API to initialize the AI session
            $response = Http::post($this->flaskApiUrl . '/initialize', [
                'session_id' => $session->id,
                'user_id' => $session->user_id,
                'agent_id' => $session->agent_id,
                'context' => $session->context,
            ]);

            if ($response->successful()) {
                // Send welcome message from the agent
                $agentData = $response->json();
                
                $welcomeMessage = ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'sender_type' => 'App\\Models\\Agent',
                    'sender_id' => $session->agent_id,
                    'content' => $agentData['welcome_message'] ?? 'Hello! How can I help you with your homework today?',
                ]);
                
                broadcast(new MessageSent($welcomeMessage));
                
                // Trigger appropriate UI components
                if (isset($agentData['ui_components']) && is_array($agentData['ui_components'])) {
                    foreach ($agentData['ui_components'] as $component) {
                        broadcast(new UIComponentTriggered(
                            $session->id,
                            $component['name'],
                            $component['config'] ?? []
                        ));
                    }
                }
                
                return true;
            } else {
                Log::error('Failed to initialize AI session', [
                    'session_id' => $session->id,
                    'response' => $response->body(),
                ]);
                
                return false;
            }
        } catch (Exception $e) {
            Log::error('Error initializing AI session', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    public function processUserMessage(ChatSession $session, ChatMessage $message)
    {
        try {
            // Call Flask API to process the message
            $response = Http::post($this->flaskApiUrl . '/process', [
                'session_id' => $session->id,
                'message_id' => $message->id,
                'user_id' => $session->user_id,
                'agent_id' => $session->agent_id,
                'content' => $message->content,
            ]);

            if ($response->successful()) {
                $aiResponse = $response->json();
                
                // Create agent response message
                $agentMessage = ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'sender_type' => 'App\\Models\\Agent',
                    'sender_id' => $session->agent_id,
                    'content' => $aiResponse['response'],
                    'metadata' => [
                        'confidence' => $aiResponse['confidence'] ?? null,
                        'intent' => $aiResponse['intent'] ?? null,
                    ],
                ]);
                
                broadcast(new MessageSent($agentMessage));
                
                // Trigger UI components if any
                if (isset($aiResponse['ui_components']) && is_array($aiResponse['ui_components'])) {
                    foreach ($aiResponse['ui_components'] as $component) {
                        broadcast(new UIComponentTriggered(
                            $session->id,
                            $component['name'],
                            $component['config'] ?? []
                        ));
                    }
                }
                
                // Apply gamification actions if any
                if (isset($aiResponse['gamification']) && is_array($aiResponse['gamification'])) {
                    $this->processGamificationActions($session, $aiResponse['gamification']);
                }
                
                return $agentMessage;
            } else {
                Log::error('Failed to process message with AI', [
                    'session_id' => $session->id,
                    'message_id' => $message->id,
                    'response' => $response->body(),
                ]);
                
                // Send a fallback message
                $fallbackMessage = ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'sender_type' => 'App\\Models\\Agent',
                    'sender_id' => $session->agent_id,
                    'content' => 'I apologize, but I encountered an issue processing your request. Could you please try again?',
                    'metadata' => [
                        'is_fallback' => true,
                    ],
                ]);
                
                broadcast(new MessageSent($fallbackMessage));
                
                return $fallbackMessage;
            }
        } catch (Exception $e) {
            Log::error('Error processing message with AI', [
                'session_id' => $session->id,
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            
            // Send a fallback message
            $fallbackMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'App\\Models\\Agent',
                'sender_id' => $session->agent_id,
                'content' => 'I apologize, but I encountered an issue processing your request. Could you please try again?',
                'metadata' => [
                    'is_fallback' => true,
                ],
            ]);
            
            broadcast(new MessageSent($fallbackMessage));
            
            return $fallbackMessage;
        }
    }

    protected function processGamificationActions(ChatSession $session, array $actions)
    {
        $gamificationService = app(GamificationService::class);
        
        foreach ($actions as $action) {
            switch ($action['type']) {
                case 'xp':
                    $gamificationService->awardXP($session->user_id, $action['amount'], $action['source'] ?? 'ai_interaction');
                    break;
                    
                case 'badge':
                    $gamificationService->awardBadge($session->user_id, $action['badge_id']);
                    break;
                    
                case 'streak':
                    $gamificationService->updateStreak($session->user_id);
                    break;
                    
                case 'retry_token':
                    $gamificationService->awardRetryToken($session->user_id, $action['amount'] ?? 1);
                    break;
                    
                case 'game':
                    $gamificationService->triggerGame($session->id, $action['game_id'], $action['config'] ?? []);
                    break;
                    
                default:
                    Log::warning('Unknown gamification action', [
                        'session_id' => $session->id,
                        'action' => $action,
                    ]);
                    break;
            }
        }
    }
}