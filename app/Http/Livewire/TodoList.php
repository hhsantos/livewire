<?php

namespace App\Http\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TodoList extends Component
{
    public $newTask = '';
    public $editingTodoId = null;
    public $editingTask = '';

    protected $rules = [
        'newTask' => 'required|min:3',
        'editingTask' => 'required|min:3',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Auth::user()->todos()->latest()->get()
        ]);
    }

    public function addTask()
    {
        $this->validate([
            'newTask' => 'required|min:3'
        ]);

        Auth::user()->todos()->create([
            'task' => $this->newTask,
            'completed' => false
        ]);

        $this->newTask = '';
    }

    public function toggleComplete($todoId)
    {
        $todo = Auth::user()->todos()->findOrFail($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function deleteTask($todoId)
    {
        Auth::user()->todos()->findOrFail($todoId)->delete();
    }
}
