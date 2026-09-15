<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory()->awaitingPayment(),
            'reference' => 'DEMO-'.Str::upper((string) Str::ulid()),
            'amount_cents' => 4900,
            'currency' => 'MYR',
            'status' => 'simulated',
            'method' => 'dummy',
            'simulated_at' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Payment $payment): void {
            $payment->invitation->forceFill(['status' => 'demo_complete'])->save();
        });
    }
}
