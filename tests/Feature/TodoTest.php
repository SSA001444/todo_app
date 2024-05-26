<?php

namespace Feature;

use App\Models\Todo;
use GuzzleHttp\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function testTodoCreation(): void
    {
         $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $todo = Todo::factory()->create();

        $this->assertDatabaseHas('todos', ['title' => $todo->title]);
    }

    public function testTodoEdition(): void
    {
        $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $todo = Todo::factory()->create([
            'user_id' => $user->id
        ]);

       $this->put('/todos/edit/'.$todo->id, [
           'title' => 'sas',
            'is_completed' => $todo->is_completed,
            'commentary' => 'dddd',
        ]);

        $this->assertDatabaseHas('todos', ['title' => 'sas']);
    }

    public function testTodoDelete(): void
    {
        $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $todo = Todo::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('todos', ['title' => $todo->title]);

        $this->get(route('todos.destroy', ['todo' => $todo->id]));

        $this->assertSoftDeleted('todos', ['title' => $todo->title]);
    }

    public function testTodoShare(): void
    {
        $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $secondUser = User::factory()->create([
            'id' => 4,
            'username' => 'S',
            'email' => 'test2@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $todo = Todo::factory()->create([
            'user_id' => $user->id
        ]);
        $url = route('todo.share', ['todo' => $todo->id]);

        $response = $this->post($url, [
            'email' => 'test2@exmaple.com'
        ]);

        $response->assertSee('Todo is shared');
    }

    public function testTodoStatusChange()
    {
        $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

       $todo = Todo::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/todos/update-status', [
                'todo_id' => $todo->id,
                'is_checked' => 'true',
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertDatabaseHas('todos', ['is_completed' => 1]);
    }

    public function testDragDropTodoOrder()
    {
        $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $todo = Todo::factory()->create();

        $todo2 = Todo::factory()->create();

        $this->actingAs($user);

        $todoIds = Todo::pluck('id')->toArray();

        $response = $this->post('/todos/reorder', [
           'todoIds' => $todoIds,
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertDatabaseHas('todos', ['sort_order' => 1]);
    }

    public function testUploadProfileImage()
    {
        $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $this->actingAs($user);

        $this->assertNull($user->profile_photo);
        $response = $this->withHeaders(['Content-Type' => 'multipart/form-data'])->post('/profile/update-photo', [
            'profile_photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($user->profile_photo);

    }
}
