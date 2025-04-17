<div class="p-6">
    @php
        dump([
            'selectedDate' => $selectedDate,
            'reservations' => $reservations->toArray(),
            'reservations_count' => $reservations->count()
        ]);
    @endphp
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

    {{-- ... resto del archivo ... --}}
</div>
