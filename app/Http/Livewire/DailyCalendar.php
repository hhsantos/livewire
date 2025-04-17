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
    // ... (resto del archivo)
}
