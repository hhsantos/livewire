<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-6">
        <!-- Formulario para agregar tarea -->
        <form wire:submit.prevent="addTask" class="mb-6">
            <div class="flex gap-4">
                <input 
                    type="text"
                    wire:model="newTask"
                    placeholder="Nueva tarea..."
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                <button 
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200"
                >
                    Agregar
                </button>
            </div>
            @error('newTask') 
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </form>

        <!-- Lista de tareas -->
        <div class="space-y-4">
            @if(isset($todos))
                @foreach($todos as $todo)
                    <div 
                        wire:key="todo-{{ $todo->id }}"
                        class="group flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                    >
                        <div class="flex items-center gap-4 flex-1">
                            <input 
                                type="checkbox" 
                                wire:click="toggleComplete({{ $todo->id }})" 
                                {{ $todo->completed ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                            <span class="{{ $todo->completed ? 'line-through text-gray-400' : 'text-gray-700' }}">
                                {{ $todo->task }}
                            </span>
                        </div>
                        <button 
                            wire:click="deleteTask({{ $todo->id }})" 
                            class="p-1 text-red-600 hover:text-red-800 focus:outline-none opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
                @if($todos->isEmpty())
                    <div class="text-center py-4 text-gray-500">
                        No hay tareas pendientes. ¡Agrega una!
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
