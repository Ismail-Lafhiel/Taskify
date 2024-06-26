<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // Retrieve tasks only for the authenticated user
        $tasks = Auth::user()->tasks;

        if ($tasks->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No tasks found for the authenticated user'], 404);
        }

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
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        try {
            $task = Task::findOrFail($id);

            if (!$this->authorize('view', $task)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }

            return response()->json(['status' => 'success', 'data' => new TaskResource($task)]);
        } catch (ModelNotFoundException $e) {
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

            return response()->json(['status' => 'success', 'message' => 'Task updated successfully', 'data' => new TaskResource($task)]);
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

            return response()->json(['status' => 'success', 'message' => 'Task ' . $task->id . ' deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Task not found'], 404);
        }
    }
    /**
     * search the specified resource from storage.
     */
    public function search($status)
    {
        $task = Task::where('status', $status)->get();
        return new TaskResource($task);
    }
}
