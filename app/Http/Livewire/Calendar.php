<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Calendar extends Component
{
    public $currentMonth;
    public $currentYear;
    public $weeks = [];
    public $monthName;
    public $reservations;
    
    public $showReservationModal = false;
    public $selectedDate;
    public $startTime;
    public $endTime;
    public $notes;
    public $editingReservation = null;
    public $showDeleteConfirmation = false;
    public $showDailyView = false;
    public $currentDate;
    
    public $searchQuery = '';
    public $dateFilter = 'all'; // 'all', 'today', 'week', 'month'
    public $reservationsCount = 0;

    // Constantes para formatos de fecha/hora
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i';
    const DB_TIME_FORMAT = 'Y-m-d H:i:s';

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->dateFilter = 'all';
        $this->reservations = collect();
        $this->loadCalendar();
        $this->loadReservations();
    }

    #[On('refreshCalendar')]
    public function refreshCalendar()
    {
        $this->loadReservations();
    }

    public function updatedSearchQuery()
    {
        $this->refreshCalendar();
    }

    public function updatedDateFilter()
    {
        $this->refreshCalendar();
    }

    public function isToday($date)
    {
        return Carbon::parse($date)->isToday();
    }

    public function openReservationModal($date, $reservationId = null)
    {
        $this->selectedDate = Carbon::parse($date)->format(self::DATE_FORMAT);
        $this->showReservationModal = false; // Reset modal first
        $this->editingReservation = null;
        $this->notes = '';

        if ($reservationId) {
            $reservation = Reservation::find($reservationId);
            if ($reservation && $reservation->user_id === Auth::id()) {
                $this->editingReservation = $reservation;
                $this->startTime = Carbon::parse($reservation->start_time)->format(self::TIME_FORMAT);
                $this->endTime = Carbon::parse($reservation->end_time)->format(self::TIME_FORMAT);
                $this->notes = $reservation->notes;
            }
        }
        $this->showReservationModal = true;
    }

    // ... (resto de métodos y lógica)

    public function render()
    {
        return view('livewire.calendar');
    }
}
