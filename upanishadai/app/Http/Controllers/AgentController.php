<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::all();
        
        return response()->json([
            'agents' => $agents
        ]);
    }

    public function show($id)
    {
        $agent = Agent::findOrFail($id);
        
        return response()->json([
            'agent' => $agent
        ]);
    }

    public function getRandomAgent()
    {
        $agent = Agent::inRandomOrder()->first();
        
        return response()->json([
            'agent' => $agent
        ]);
    }
}