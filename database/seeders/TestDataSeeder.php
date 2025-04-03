<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario de prueba
        $user = User::factory()->create([
            'name' => 'Usuario de Prueba',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Crear algunas reservas de ejemplo
        $dates = [
            Carbon::now()->addDays(1),
            Carbon::now()->addDays(2),
            Carbon::now()->addDays(3)
        ];

        foreach ($dates as $date) {
            Reservation::create([
                'user_id' => $user->id,
                'reservation_date' => $date->format('Y-m-d'),
                'start_time' => $date->copy()->setTime(10, 0),
                'end_time' => $date->copy()->setTime(11, 0),
                'notes' => 'Reserva de prueba para ' . $date->format('Y-m-d')
            ]);
        }
    }
}
