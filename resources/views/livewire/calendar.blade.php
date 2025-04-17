<div class="p-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <!-- Barra de búsqueda y filtros -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex-1 max-w-sm">
                    <input
                        type="text"
                        wire:model.live="searchQuery"
                        placeholder="Buscar en las notas..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                </div>
                
                <div class="flex space-x-4 ml-4">
                    <select
                        wire:model.live="dateFilter"
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                        <option value="all">Todas las fechas</option>
                        <option value="today">Hoy</option>
                        <option value="week">Esta semana</option>
                        <option value="month">Este mes</option>
                    </select>
                    
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-md">
                        Reservas: <span class="font-bold">{{ $reservationsCount }}</span>
                    </div>
                </div>
            </div>

            <!-- Navegación del calendario -->
            <div class="flex items-center justify-between mb-4">
                <button wire:click="previousMonth" class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded">
                    &larr; Mes anterior
                </button>
                <h2 class="text-xl font-semibold">{{ $monthName }}</h2>
                <button wire:click="nextMonth" class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded">
                    Mes siguiente &rarr;
                </button>
            </div>

            <!-- ... resto del archivo ... -->
        </div>
    </div>
</div>
