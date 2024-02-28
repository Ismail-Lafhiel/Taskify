<?php

namespace Tests\Unit\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new task.
     *
     * @return void
     */
    public function testCreateNewTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/tasks', [
            'name' => 'New Task',
            'description' => 'Task description',
            'status' => 'todo',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('tasks', [
            'name' => 'New Task',
            'description' => 'Task description',
            'status' => 'todo',
        ]);
    }

    /**
     * Test updating a task.
     *
     * @return void
     */
    public function testUpdateTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'name' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'doing',
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'doing',
        ]);
    }

    /**
     * Test deleting a task.
     *
     * @return void
     */
    public function testDeleteTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
