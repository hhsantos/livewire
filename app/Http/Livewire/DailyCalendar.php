<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use Carbon\Carbon;

class DailyCalendar extends Component
{
    public $selectedDate;
    public $reservations = [];
    public $hours = [];
    public $showReservationModal = false;
    public $selectedHour = null;
    public $startTime;
    public $endTime;
    public $notes;
    public $editingReservationId = null;

    // Constantes para formatos de fecha/hora
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i';
    const DB_TIME_FORMAT = 'Y-m-d H:i:s';

    public function mount($selectedDate = null)
    {
        $this->selectedDate = $selectedDate ?? now()->format(self::DATE_FORMAT);
        $this->reservations = collect();
        $this->initializeHours();
    }

    // ... (resto de métodos y lógica)

    public function render()
    {
        return view('livewire.daily-calendar');
    }
}
