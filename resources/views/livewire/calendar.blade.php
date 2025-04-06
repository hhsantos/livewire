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

        <!-- Calendario -->
        <div class="border border-gray-200 rounded overflow-hidden">
            <div class="grid grid-cols-7 gap-px bg-gray-200">
            <!-- Encabezados -->
            @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dayName)
                <div class="bg-gray-50 p-2 text-center text-gray-700">
                    {{ $dayName }}
                </div>
            @endforeach

            <!-- Días -->
            @foreach ($weeks as $weekIndex => $week)
                @foreach ($week as $day)
                    <div class="{{ !$day || $day->month !== $currentDate->month ? 'bg-gray-100' : 'bg-white' }}">
                        @if ($day)
                            <div class="h-[120px] relative">
                                <div class="p-1 border-b {{ $day->month === $currentDate->month ? 'hover:bg-blue-50 cursor-pointer' : '' }}" 
                                     @if($day->month === $currentDate->month)
                                        wire:click="viewDailyCalendar('{{ $day->format('Y-m-d') }}')" 
                                     @endif>
                                    <span class="text-sm {{ $day->isToday() ? 'bg-blue-500 text-white rounded-full w-6 h-6 inline-flex items-center justify-center' : ($day->month !== $currentDate->month ? 'text-gray-400' : '') }}">
                                        {{ $day->format('j') }}
                                    </span>
                                </div>
                                <div class="overflow-y-auto absolute inset-x-0 bottom-0 top-8 p-1">
                                    @php
                                        $dateString = $day->format('Y-m-d');
                                        $dayReservations = isset($reservations[$dateString]) ? $reservations[$dateString] : collect();
                                    @endphp
                                    @if($dayReservations->count() > 0)
                                        @foreach ($dayReservations as $reservation)
                                            <div class="mb-1 p-1 text-xs rounded {{ Auth::id() === $reservation->user_id ? 'bg-blue-100 text-blue-700 cursor-pointer hover:bg-blue-200' : 'bg-gray-100' }}"
                                                @if (Auth::id() === $reservation->user_id)
                                                    wire:click.stop="openReservationModal('{{ $dateString }}', {{ $reservation->id }})"
                                                @endif
                                            >
                                                <div class="font-medium truncate">{{ $reservation->user->name }}</div>
                                                <div class="text-xs opacity-75 truncate">
                                                    {{ Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                                                </div>
                                                @if(!empty($reservation->notes))
                                                    <div class="text-xs text-gray-500 truncate" title="{{ $reservation->notes }}">{{ $reservation->notes }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endforeach
            </div>
        </div>

        <!-- Modal de Reserva -->
        @if($showReservationModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative mx-auto p-6 border w-96 shadow-2xl rounded-lg bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">{{ $editingReservation ? 'Editar Reserva' : 'Nueva Reserva' }}</h3>
                    <button wire:click="closeReservationModal" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        &times;
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                    <div class="text-gray-900">{{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') : '' }}</div>
                </div>

                <div class="mb-4">
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Hora de inicio</label>
                    <select wire:model="startTime" id="start_time" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        @for ($i = 0; $i < 24; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00</option>
                        @endfor
                    </select>
                    @error('startTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Hora de fin</label>
                    <select wire:model="endTime" id="end_time" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        @for ($i = 0; $i < 24; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00</option>
                        @endfor
                    </select>
                    @error('endTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                    <textarea wire:model="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                </div>

                <div class="flex justify-between mt-6">
                    @if($editingReservation)
                        <div>
                            @if(!$showDeleteConfirmation)
                                <button wire:click="confirmDelete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                    Eliminar
                                </button>
                            @else
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-red-600">¿Confirmar eliminación?</span>
                                    <button wire:click="deleteReservation" class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                        Sí
                                    </button>
                                    <button wire:click="$set('showDeleteConfirmation', false)" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        No
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div></div>
                    @endif
                    <div class="flex space-x-3">
                        <button wire:click="closeReservationModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-md">
                            Cancelar
                        </button>
                        <button wire:click="saveReservation" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            {{ $editingReservation ? 'Guardar Cambios' : 'Crear Reserva' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Vista Diaria -->
        @if($showDailyView)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative mx-auto p-4 w-full max-w-6xl bg-white shadow-2xl rounded-lg mt-8">
                <button wire:click="closeDailyView" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @livewire('daily-calendar', ['selectedDate' => $selectedDate], key($selectedDate))
            </div>
        </div>
        @endif
    </div>
</div>
