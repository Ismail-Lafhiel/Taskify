<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve tasks only for the authenticated user
        $tasks = Auth::user()->tasks;
        return $tasks;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "description" => "required",
            "status" => "required|in:todo,doing,done",
        ]);

        // Create task for the authenticated user
        $task = Auth::user()->tasks()->create($request->all());
        return $task;
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Check if the task belongs to the authenticated user
        if (Auth::id() === $task->user_id) {
            return $task;
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Check if the task belongs to the authenticated user
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            "name" => "required",
            "description" => "required",
            "status" => "required|in:todo,doing,done",
        ]);

        $task->update($request->all());
        return $task;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Check if the task belongs to the authenticated user
        if (Auth::id() !== $task->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $task->delete();
    }
}
