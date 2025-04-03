<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'title' => 'Test Task',
            'description' => 'Some description',
            'status' => 'new',
        ];
        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Task']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_only_see_their_tasks()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Task::factory()->count(2)->for($user)->create(['status' => 'new']);
        Task::factory()->for($otherUser)->create(['status' => 'new']);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tasks?status=new');

        $response->assertStatus(200);
        $tasks = $response->json();

        $this->assertCount(2, $tasks);

        foreach ($tasks as $task) {
            $this->assertEquals($user->id, $task['user_id']);
            $this->assertEquals('new', $task['status']);
        }
    }

    public function test_user_can_update_and_delete_their_task()
    {
        $task = Task::factory()->create(['status' => 'new']);
        $user = $task->user;
        Sanctum::actingAs($user);


        $updateData = ['title' => $task->title, 'description' => $task->description, 'status' => 'completed'];
        $resUpdate = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $resUpdate->assertStatus(200)
            ->assertJsonFragment(['status' => 'completed']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);


        $resDelete = $this->deleteJson("/api/tasks/{$task->id}");
        $resDelete->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_access_another_users_task()
    {
        $task = Task::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);


        $resShow = $this->getJson("/api/tasks/{$task->id}");
        $resShow->assertStatus(404);


        $resUpdate = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked',
            'status' => 'completed'
        ]);

        $resUpdate->assertStatus(403);
    }


}
