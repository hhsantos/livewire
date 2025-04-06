<div class="p-6">
    {{-- Navegación --}}
    <div class="flex items-center justify-between mb-6">
        <button wire:click="previousDay" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            &larr; Día anterior
        </button>
        <div class="text-xl font-semibold">
            {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}
        </div>
        <button wire:click="nextDay" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Día siguiente &rarr;
        </button>
    </div>

    {{-- Grid de horas --}}
    <div class="border rounded-lg overflow-hidden">
        @foreach($hours as $timeSlot)
            <div class="flex border-b last:border-b-0">
                {{-- Hora --}}
                <div class="w-24 p-4 bg-gray-50 border-r text-sm font-medium text-gray-500">
                    {{ $timeSlot['hour'] }}
                </div>
                {{-- Slot de reserva --}}
                <div class="flex-1 p-4 relative min-h-[4rem]" wire:click="openCreateModal('{{ $timeSlot['hour'] }}')">
                    @foreach($reservations as $reservation)
                        @if($reservation->start_time->format('H:i') === $timeSlot['hour'])
                            <div class="absolute inset-x-4 bg-blue-100 border border-blue-200 rounded-lg p-3 cursor-pointer"
                                 wire:click.stop="openEditModal({{ $reservation->id }})">
                                <div class="font-medium text-blue-900">
                                    {{ $reservation->start_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}
                                </div>
                                <div class="text-sm text-blue-700">
                                    {{ $reservation->user->name }}
                                </div>
                                @if($reservation->notes)
                                    <div class="text-sm text-blue-600 mt-1">
                                        {{ $reservation->notes }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal de Reserva --}}
    @if($showReservationModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingReservationId ? 'Editar Reserva' : 'Nueva Reserva' }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora de inicio</label>
                        <select wire:model="startTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @for ($i = 0; $i < 24; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00</option>
                            @endfor
                        </select>
                        @error('startTime') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora de fin</label>
                        <select wire:model="endTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @for ($i = 0; $i < 24; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    @if($editingReservationId)
                        <button wire:click="deleteReservation" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                            Eliminar
                        </button>
                    @endif
                    <button wire:click="resetModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button wire:click="saveReservation" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
