<?php

namespace App\Jobs;

use App\Mail\ReceiptMail;
use App\Models\AuditLog;
use App\Models\Receipt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendReceiptEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 45;

    public function __construct(public int $receiptId) {}

    public function backoff(): array
    {
        return [60, 300, 900, 1800];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function (): void {
            $receipt = Receipt::query()->lockForUpdate()->find($this->receiptId);
            if (! $receipt || $receipt->emailed_at) {
                return;
            }
            Mail::mailer('receipts')->to($receipt->details['customer_email'])->send(new ReceiptMail($receipt));
            $receipt->update(['email_status' => 'sent', 'emailed_at' => now()]);
            AuditLog::create([
                'user_id' => $receipt->user_id, 'action' => 'receipt.email_sent', 'outcome' => 'success',
                'method' => 'QUEUE', 'status_code' => 200,
                'subject_type' => 'receipt', 'subject_id' => $receipt->id,
            ]);
        });
    }

    public function failed(?Throwable $exception): void
    {
        $receipt = Receipt::find($this->receiptId);
        if ($receipt && ! $receipt->emailed_at) {
            $receipt->update(['email_status' => 'failed']);
            AuditLog::create([
                'user_id' => $receipt->user_id, 'action' => 'receipt.email_failed', 'outcome' => 'failed',
                'method' => 'QUEUE', 'status_code' => 500,
                'subject_type' => 'receipt', 'subject_id' => $receipt->id,
            ]);
        }
    }
}
