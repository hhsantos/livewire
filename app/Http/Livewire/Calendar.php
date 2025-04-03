<?php

namespace App\Http\Livewire;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

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
    public $currentDate;
    
    // Nuevas propiedades para búsqueda y filtrado
    public $searchQuery = '';
    public $dateFilter = 'all'; // 'all', 'today', 'week', 'month'
    public $reservationsCount = 0;

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
        $this->selectedDate = Carbon::parse($date);
        $this->startTime = null;
        $this->endTime = null;
        $this->notes = '';
        $this->editingReservation = null;

        if ($reservationId) {
            $reservation = Reservation::find($reservationId);
            if ($reservation && $reservation->user_id === Auth::id()) {
                $this->editingReservation = $reservation;
                $this->startTime = Carbon::parse($reservation->start_time)->format('H:i');
                $this->endTime = Carbon::parse($reservation->end_time)->format('H:i');
                $this->notes = $reservation->notes;
            }
        }

        $this->showReservationModal = true;
    }

    public function closeReservationModal()
    {
        $this->showReservationModal = false;
        $this->resetReservationForm();
    }

    private function resetReservationForm()
    {
        $this->selectedDate = null;
        $this->editingReservation = null;
        $this->showDeleteConfirmation = false;
        $this->startTime = null;
        $this->endTime = null;
        $this->notes = null;
    }

    public function saveReservation()
    {
        $this->validate([
            'startTime' => 'required|date_format:H:i',
            'endTime' => ['required', 'date_format:H:i', 'after:startTime'],
            'notes' => 'nullable|string|max:255'
        ]);

        if ($this->editingReservation) {
            if ($this->editingReservation->user_id !== Auth::id()) {
                return;
            }

            $this->editingReservation->update([
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'notes' => $this->notes
            ]);
        } else {
            Reservation::create([
                'user_id' => Auth::id(),
                'reservation_date' => $this->selectedDate,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'notes' => $this->notes
            ]);
        }

        $this->showReservationModal = false;
        $this->resetReservationForm();
        $this->refreshCalendar();
    }

    public function confirmDelete()
    {
        $this->showDeleteConfirmation = true;
    }

    public function deleteReservation()
    {
        if ($this->editingReservation && $this->editingReservation->user_id === Auth::id()) {
            $this->editingReservation->delete();
            $this->showReservationModal = false;
            $this->showDeleteConfirmation = false;
            $this->resetReservationForm();
            $this->refreshCalendar();
        }
    }

    public function previousMonth()
    {
        $this->currentDate = $this->currentDate->copy()->subMonth();
        $this->dateFilter = 'all'; // Resetear el filtro al navegar
        $this->loadCalendar();
        $this->refreshCalendar();
    }

    public function nextMonth()
    {
        $this->currentDate = $this->currentDate->copy()->addMonth();
        $this->dateFilter = 'all'; // Resetear el filtro al navegar
        $this->loadCalendar();
        $this->refreshCalendar();
    }

    private function loadReservations()
    {
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();

        $query = Reservation::query();

        // Aplicar filtros de fecha según el modo seleccionado
        if ($this->dateFilter === 'all') {
            // En modo 'all', mostrar las reservas del mes actual del calendario
            $query->whereBetween('reservation_date', [$startOfMonth, $endOfMonth]);
        } else {
            // Aplicar otros filtros de fecha
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('reservation_date', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('reservation_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('reservation_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
            }
        }

        // Búsqueda por notas
        if (!empty($this->searchQuery)) {
            $query->where('notes', 'like', '%' . $this->searchQuery . '%');
        }

        $reservations = $query->with(['user'])
                            ->orderBy('reservation_date', 'asc')
                            ->orderBy('start_time', 'asc')
                            ->get();

        $this->reservationsCount = $reservations->count();
        $this->reservations = collect();

        foreach ($reservations as $reservation) {
            $dateKey = $reservation->reservation_date->format('Y-m-d');
            if (!$this->reservations->has($dateKey)) {
                $this->reservations[$dateKey] = collect();
            }
            $this->reservations[$dateKey]->push($reservation);
        }
    }

    private function loadCalendar()
    {
        $this->monthName = Carbon::createFromDate($this->currentDate->year, $this->currentDate->month, 1)->locale('es')->isoFormat('MMMM Y');

        $firstDayOfMonth = Carbon::createFromDate($this->currentDate->year, $this->currentDate->month, 1)->startOfMonth();
        $lastDayOfMonth = Carbon::createFromDate($this->currentDate->year, $this->currentDate->month, 1)->endOfMonth();

        $startDate = $firstDayOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endDate = $lastDayOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $currentDate = $startDate->copy();
        $weeks = [];
        $currentWeek = [];

        while ($currentDate <= $endDate) {
            if ($currentDate->dayOfWeek === Carbon::MONDAY && !empty($currentWeek)) {
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }

            if ($currentDate->month === $this->currentDate->month) {
                $currentWeek[] = $currentDate->copy();
            } else {
                $currentWeek[] = null;
            }

            $currentDate->addDay();
        }

        if (!empty($currentWeek)) {
            $weeks[] = $currentWeek;
        }

        $this->weeks = $weeks;
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
