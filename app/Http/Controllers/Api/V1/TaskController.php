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

        // Creating the task for the authenticated user
        $task = Auth::user()->tasks()->create($request->all());
        return $task;
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        try {
            $task = Task::findOrFail($task->id);

            if (!$this->authorize('view', $task)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }

            return response()->json(['status' => 'success', 'data' => $task]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->authorize('update', $task)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                "name" => "required",
                "description" => "required",
                "status" => "required|in:todo,doing,done",
            ]);

            $task->update($request->all());

            return response()->json(['status' => 'success', 'message' => 'Task updated successfully', 'data' => $task]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->authorize('delete', $task)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }

            $task->delete();

            return response()->json(['status' => 'success', 'message' => 'Task '.$task->id.' deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
        }
    }
    /**
     * search the specified resource from storage.
     */
    public function search($status)
    {
        return Task::where('status', $status)->get();
    }
}
