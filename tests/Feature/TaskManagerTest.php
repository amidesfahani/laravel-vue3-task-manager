<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class TaskManagerTest extends TestCase
{
	protected $user;

	protected function setUp(): void
	{
		parent::setUp();

		$this->user = User::factory()->create();
		Sanctum::actingAs($this->user);
	}

	public function test_can_create_task()
	{
		$taskData = [
			'title' => 'Test Task',
			'description' => 'This is a test task',
			'status' => 'pending',
			'start_date' => now()->toISOString(),
			'end_date' => now()->addDay()->toISOString(),
		];

		$response = $this->postJson('/api/tasks', $taskData);

		$response->assertStatus(201)->assertJsonStructure([
			'data' => [
				'id',
				'title',
				'description',
				'status',
				'start_date',
				'end_date',
				'created_at',
				'updated_at',
			]
		])->assertJsonFragment(['title' => 'Test Task']);

		$this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
	}

	public function test_create_task_fails_with_invalid_data()
	{
		$taskData = [
			'title' => '',
			'status' => 'invalid_status',
			'start_date' => 'not-a-date',
		];

		$response = $this->postJson('/api/tasks', $taskData);

		$response->assertStatus(422)->assertJsonValidationErrors(['title', 'status', 'start_date']);
	}

	public function test_can_retrieve_tasks()
	{
		Task::factory()->count(3)->create(['user_id' => $this->user->id]);

		$response = $this->getJson('/api/tasks');

		$response->assertStatus(200)->assertJsonStructure([
			'data' => [
				'*' => [
					'id',
					'title',
					'description',
					'status',
					'start_date',
					'end_date',
					'created_at',
					'updated_at',
				]
			],
			'links',
			'meta',
		])->assertJsonCount(3, 'data');
	}

	public function test_can_retrieve_single_task()
	{
		$task = Task::factory()->create(['user_id' => $this->user->id]);

		$response = $this->getJson("/api/tasks/{$task->id}");

		$response->assertStatus(200)->assertJsonStructure([
			'data' => [
				'id',
				'title',
				'description',
				'status',
				'start_date',
				'end_date',
				'created_at',
				'updated_at',
			]
		])->assertJsonFragment(['title' => $task->title]);
	}

	public function test_retrieve_non_existent_task_fails()
	{
		$response = $this->getJson('/api/tasks/999');

		$response->assertStatus(404);
	}

	public function test_can_update_task()
	{
		$task = Task::factory()->create(['user_id' => $this->user->id]);

		$updatedData = [
			'title' => 'Updated Task',
			'description' => 'Updated description',
			'status' => 'completed',
			'start_date' => now()->toISOString(),
			'end_date' => now()->addDay()->toISOString(),
		];

		$response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

		$response->assertStatus(200)->assertJsonFragment(['title' => 'Updated Task']);

		$this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Task']);
	}

	public function test_update_task_fails_with_invalid_data()
	{
		$task = Task::factory()->create(['user_id' => $this->user->id]);

		$updatedData = [
			'title' => '',
			'status' => 'invalid_status',
		];

		$response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

		$response->assertStatus(422)->assertJsonValidationErrors(['title', 'status']);
	}

	public function test_can_delete_task()
	{
		$task = Task::factory()->create(['user_id' => $this->user->id]);

		$response = $this->deleteJson("/api/tasks/{$task->id}");

		$response->assertStatus(204);

		$this->assertDatabaseMissing('tasks', ['id' => $task->id]);
	}

	public function test_delete_non_existent_task_fails()
	{
		$response = $this->deleteJson('/api/tasks/999');

		$response->assertStatus(404);
	}
}
