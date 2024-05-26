<p>Todo for you:</p>
<p>Title: {{ $todo->title }}</p>
<p>This todo shared from: {{ \App\Models\User::where('id', $todo->user_id)->first()->email }}</p>

