<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Invitation> */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => 'Majlis Perkahwinan Arif & Najihah',
            'theme' => 'perkahwinan',
            'host_name' => fake()->name(),
            'celebrant_name' => 'Arif & Najihah',
            'event_date' => today()->addMonth(),
            'start_time' => '11:00',
            'end_time' => '16:00',
            'venue' => 'Dewan Seri Indah',
            'address' => 'Jalan Melati, Kuala Lumpur',
            'contact_name' => fake()->name(),
            'contact_phone' => '60123456789',
            'status' => 'draft',
        ];
    }

    public function awaitingPayment(): static
    {
        return $this->state(fn () => [
            'package_code' => 'mawar', 'package_name' => 'Pakej Mawar',
            'design_code' => 'MD001', 'design_name' => 'Rimbun Kasih',
            'amount_cents' => 4900, 'status' => 'awaiting_payment',
        ]);
    }
}
