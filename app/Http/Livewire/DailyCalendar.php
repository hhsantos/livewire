<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Attributes\On;

class DailyCalendar extends Component
{
    public $selectedDate;
    public $reservations = [];
    public $hours = [];
    
    // Modal properties
    public $showReservationModal = false;
    public $selectedHour = null;
    public $startTime;
    public $endTime;
    public $notes;
    public $editingReservationId = null;

    public function mount($selectedDate = null)
    {
        logger()->info('DailyCalendar - Mount with date:', ['selectedDate' => $selectedDate]);
        $this->selectedDate = $selectedDate ?? now()->format('Y-m-d');
        logger()->info('DailyCalendar - Initialized with date:', ['selectedDate' => $this->selectedDate]);
        $this->initializeHours();
        $this->loadReservations();
    }

    #[On('setDailyDate')]
    public function setDate($date)
    {
        logger()->info('DailyCalendar - Received date:', ['date' => $date]);
        if ($date) {
            $this->selectedDate = $date;
            logger()->info('DailyCalendar - Set date:', ['selectedDate' => $this->selectedDate]);
            $this->loadReservations();
        }
    }

    #[On('resetDailyCalendar')]
    public function resetCalendar()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadReservations();
    }

    public function initializeHours()
    {
        $this->hours = collect(range(0, 23))->map(function ($hour) {
            $currentHour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $nextHour = str_pad(($hour + 1) % 24, 2, '0', STR_PAD_LEFT);
            return [
                'hour' => $currentHour . ':00',
                'value' => $currentHour,
                'formatted' => $currentHour . ':00 - ' . $nextHour . ':00'
            ];
        })->toArray();
    }

    public function resetModal()
    {
        $this->showReservationModal = false;
        $this->selectedHour = null;
        $this->startTime = null;
        $this->endTime = null;
        $this->notes = '';
        $this->editingReservationId = null;
    }

    public function loadReservations()
    {
        if ($this->selectedDate) {
            $this->reservations = Reservation::whereDate('reservation_date', $this->selectedDate)
                ->with('user')
                ->orderBy('start_time')
                ->get();
        }
    }

    public function previousDay()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
        $this->loadReservations();
    }

    public function nextDay()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
        $this->loadReservations();
    }

    public function openCreateModal($hour)
    {
        $this->resetModal();
        $hourValue = (int) substr($hour, 0, 2);
        $this->selectedHour = $hourValue;
        $this->startTime = str_pad($hourValue, 2, '0', STR_PAD_LEFT);
        $this->endTime = str_pad(($hourValue + 1) % 24, 2, '0', STR_PAD_LEFT);
        $this->showReservationModal = true;
    }

    public function openEditModal(Reservation $reservation)
    {
        // Ensure only the owner can modify the reservation
        if ($reservation->user_id !== auth()->id()) {
            // You can also optionally dispatch a browser event to notify unauthorized access
            // $this->dispatchBrowserEvent('notification', ['type' => 'error', 'message' => 'Acceso no autorizado.']);
            return;
        }
        $this->resetModal();
        $this->editingReservationId = $reservation->id;
        $this->startTime = $reservation->start_time->format('H');
        $this->endTime = $reservation->end_time->format('H');
        $this->notes = $reservation->notes;
        $this->showReservationModal = true;
    }

    public function saveReservation()
    {
        $this->validate([
            'startTime' => 'required',
            'endTime' => 'required|different:startTime',
            'notes' => 'nullable|string'
        ]);

        // Check if a reservation already exists for the selected hour
        $requestedStart = Carbon::parse($this->selectedDate . ' ' . $this->startTime . ':00');
        $requestedEnd = Carbon::parse($this->selectedDate . ' ' . $this->endTime . ':00');
        if ((int)$this->endTime <= (int)$this->startTime) {
            $requestedEnd->addDay();
        }

        $conflict = Reservation::whereDate('reservation_date', $this->selectedDate)
            ->where(function($query) use ($requestedStart, $requestedEnd) {
                $query->where(function($q) use ($requestedStart, $requestedEnd) {
                    // Nueva reserva comienza durante una existente
                    $q->where('start_time', '<=', $requestedStart)
                      ->where('end_time', '>', $requestedStart);
                })->orWhere(function($q) use ($requestedStart, $requestedEnd) {
                    // Nueva reserva termina durante una existente
                    $q->where('start_time', '<', $requestedEnd)
                      ->where('end_time', '>=', $requestedEnd);
                })->orWhere(function($q) use ($requestedStart, $requestedEnd) {
                    // Nueva reserva engloba completamente una existente
                    $q->where('start_time', '>=', $requestedStart)
                      ->where('end_time', '<=', $requestedEnd);
                });
            })
            ->when($this->editingReservationId, function($query) {
                return $query->where('id', '<>', $this->editingReservationId);
            })->first();

        if ($conflict) {
            $this->addError('startTime', 'Ya existe una reserva que se solapa con el horario seleccionado.');
            return;
        }

        if ($this->editingReservationId) {
            $reservation = Reservation::find($this->editingReservationId);
        } else {
            $reservation = new Reservation();
            $reservation->user_id = auth()->id();
        }
        $reservation->reservation_date = $this->selectedDate;

        $startTime = $this->startTime . ':00';
        $endTime = $this->endTime . ':00';

        $reservation->start_time = Carbon::parse($this->selectedDate . ' ' . $startTime);
        $reservation->end_time = Carbon::parse($this->selectedDate . ' ' . $endTime);

        // If the end time is less than or equal to the start time, assume it spans to the next day
        if ((int)$this->endTime <= (int)$this->startTime) {
            $reservation->end_time->addDay();
        }

        $reservation->notes = $this->notes;
        $reservation->save();

        $this->resetModal();
        $this->loadReservations();
    }

    public function deleteReservation()
    {
        if ($this->editingReservationId) {
            Reservation::destroy($this->editingReservationId);
            $this->resetModal();
            $this->loadReservations();
        }
    }

    public function render()
    {
        return view('livewire.daily-calendar');
    }
}
