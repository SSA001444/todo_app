<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
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
}
