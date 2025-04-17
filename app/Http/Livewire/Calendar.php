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
    public $dateFilter = 'all';
    // ... (resto del archivo)
}
