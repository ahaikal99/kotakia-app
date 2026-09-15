<?php

namespace App\Services;

use App\Jobs\SendReceiptEmail;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    public function issue(Payment $payment, bool $email = true): Receipt
    {
        return DB::transaction(function () use ($payment, $email): Receipt {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            abort_unless(in_array($payment->status, ['simulated', 'paid'], true), 409, 'Bayaran belum selesai.');
            if ($receipt = $payment->receipt()->first()) {
                return $receipt;
            }
            $invitation = $payment->invitation()->with('user')->firstOrFail();
            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'user_id' => $invitation->user_id,
                'number' => 'KT-'.($payment->simulated_at ?? $payment->created_at)->format('Y').'-'.str_pad($payment->id, 6, '0', STR_PAD_LEFT),
                'email_status' => $email ? 'queued' : 'not_sent',
                'details' => [
                    'customer_name' => $invitation->user->name,
                    'customer_email' => $invitation->user->email,
                    'order_number' => 'KT'.str_pad($invitation->id, 5, '0', STR_PAD_LEFT),
                    'title' => $invitation->title,
                    'design' => $invitation->design_name.' · '.$invitation->design_code,
                    'issued_at' => ($payment->simulated_at ?? $payment->created_at)->timezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i'),
                    'reference' => $payment->reference,
                    'paid' => $payment->status === 'paid',
                    'currency' => $payment->currency,
                    'total_cents' => $payment->amount_cents,
                    'items' => [
                        ['name' => $invitation->package_name, 'description' => 'Kad jemputan digital', 'amount_cents' => $payment->amount_cents, 'included' => false],
                        ['name' => 'Servis', 'description' => 'Termasuk dalam pakej', 'amount_cents' => 0, 'included' => true],
                        ['name' => 'Setup kad', 'description' => 'Termasuk dalam pakej', 'amount_cents' => 0, 'included' => true],
                    ],
                ],
            ]);
            if ($email) {
                SendReceiptEmail::dispatch($receipt->id)->onConnection('database')->onQueue('receipts')->beforeCommit();
            }

            return $receipt;
        });
    }
}
