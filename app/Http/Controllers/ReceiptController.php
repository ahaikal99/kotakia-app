<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Jobs\SendReceiptEmail;
use App\Models\Receipt;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function show(Request $request, int $invitation, ReceiptService $service): View
    {
        $card = $request->user()->invitations()->with('payment')->findOrFail($invitation);
        abort_unless($card->payment, 404);
        $receipt = $service->issue($card->payment, false);
        AuditContext::mark($request, 'receipt.viewed', $receipt);

        return view('receipts.show', compact('receipt'));
    }

    public function send(Request $request, int $invitation, ReceiptService $service): RedirectResponse
    {
        $card = $request->user()->invitations()->with('payment')->findOrFail($invitation);
        abort_unless($card->payment, 404);
        $receipt = $service->issue($card->payment, false);
        DB::transaction(function () use ($receipt, $request): void {
            $receipt = Receipt::query()->lockForUpdate()->findOrFail($receipt->id);
            if (in_array($receipt->email_status, ['not_sent', 'failed'], true)) {
                $receipt->update(['email_status' => 'queued']);
                SendReceiptEmail::dispatch($receipt->id)->onConnection('database')->onQueue('receipts')->beforeCommit();
                AuditContext::mark($request, 'receipt.email_requested', $receipt);
            }
        });

        return to_route('receipts.show', $invitation)->with('status', 'Sila semak status penghantaran email di bawah.');
    }
}
