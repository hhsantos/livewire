<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Http\Livewire\Calendar;
use Carbon\Carbon;

class CalendarTest extends TestCase
{
    // use RefreshDatabase; // Comentado para no limpiar la base de datos en producción

    public function test_can_create_reservation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $date = now()->format('Y-m-d');
        $startTime = '10:00';
        $endTime = '11:00';
        $notes = 'Test reservation';

        Livewire::test(Calendar::class)
            ->set('selectedDate', $date)
            ->set('startTime', $startTime)
            ->set('endTime', $endTime)
            ->set('notes', $notes)
            ->call('saveReservation');

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'reservation_date' => $date,
            'notes' => $notes
        ]);

        $reservation = Reservation::first();
        $this->assertEquals($startTime, $reservation->start_time->format('H:i'));
        $this->assertEquals($endTime, $reservation->end_time->format('H:i'));
    }

    public function test_can_edit_reservation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una reserva inicial
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'reservation_date' => now(),
            'start_time' => now()->setTimeFromTimeString('10:00'),
            'end_time' => now()->setTimeFromTimeString('11:00'),
            'notes' => 'Original note'
        ]);

        // Nuevos datos para la edición
        $newStartTime = '14:00';
        $newEndTime = '15:00';
        $newNotes = 'Updated note';

        Livewire::test(Calendar::class)
            ->set('editingReservation', $reservation)
            ->set('selectedDate', $reservation->reservation_date->format('Y-m-d'))
            ->set('startTime', $newStartTime)
            ->set('endTime', $newEndTime)
            ->set('notes', $newNotes)
            ->call('saveReservation');

        $updatedReservation = Reservation::find($reservation->id);
        $this->assertEquals($newStartTime, $updatedReservation->start_time->format('H:i'));
        $this->assertEquals($newEndTime, $updatedReservation->end_time->format('H:i'));
        $this->assertEquals($newNotes, $updatedReservation->notes);
    }

    public function test_cannot_edit_others_reservation()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Crear una reserva para user1
        $reservation = Reservation::create([
            'user_id' => $user1->id,
            'reservation_date' => now(),
            'start_time' => now()->setTimeFromTimeString('10:00'),
            'end_time' => now()->setTimeFromTimeString('11:00'),
            'notes' => 'Original note'
        ]);

        // Intentar editar como user2
        $this->actingAs($user2);

        Livewire::test(Calendar::class)
            ->set('editingReservation', $reservation)
            ->set('selectedDate', $reservation->reservation_date->format('Y-m-d'))
            ->set('startTime', '14:00')
            ->set('endTime', '15:00')
            ->set('notes', 'Attempted update')
            ->call('saveReservation');

        // Verificar que la reserva no cambió
        $unchangedReservation = Reservation::find($reservation->id);
        $this->assertEquals('10:00', $unchangedReservation->start_time->format('H:i'));
        $this->assertEquals('11:00', $unchangedReservation->end_time->format('H:i'));
        $this->assertEquals('Original note', $unchangedReservation->notes);
    }
}
