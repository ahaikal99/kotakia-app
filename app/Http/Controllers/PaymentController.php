<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Models\AccessControl;
use App\Services\ReceiptService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, int $invitation): View|RedirectResponse
    {
        $card = $request->user()->invitations()->with('payment')->findOrFail($invitation);
        if ($card->status === 'draft' || ! $card->design_code) {
            return to_route('invitations.options', $card)->with('status', 'Pilih pakej dan reka bentuk terlebih dahulu.');
        }

        return view('payments.show', ['invitation' => $card]);
    }

    public function simulate(Request $request, int $invitation): RedirectResponse
    {
        DB::transaction(function () use ($request, $invitation): void {
            $control = AccessControl::where('key', 'payments')->lockForUpdate()->first();
            if ($control?->is_blocked && ! $request->user()->isManager()) {
                AuditContext::mark($request, 'access.maintenance_blocked', $control);
                throw new HttpResponseException(response()->view('access-blocked', ['title' => 'Bayaran dihentikan sementara', 'reason' => $control->reason], 503));
            }
            $card = $request->user()->invitations()->lockForUpdate()->findOrFail($invitation);
            if ($card->status === 'demo_complete') {
                AuditContext::mark($request, 'payment.simulation_repeated', $card);

                return;
            }
            abort_unless($card->status === 'awaiting_payment' && $card->amount_cents !== null && $card->package_code && $card->design_code, 409, 'Lengkapkan pilihan pakej dan reka bentuk terlebih dahulu.');
            $payment = $card->payment()->create([
                'reference' => 'DEMO-'.Str::upper((string) Str::ulid()),
                'amount_cents' => $card->amount_cents,
                'currency' => 'MYR',
                'status' => 'simulated',
                'method' => 'dummy',
                'simulated_at' => now(),
            ]);
            $card->forceFill(['status' => 'demo_complete'])->save();
            app(ReceiptService::class)->issue($payment);
            AuditContext::mark($request, 'payment.simulated', $payment);
        });

        return to_route('payments.show', $invitation)->with('status', 'Tempahan berjaya direkodkan. Tiada wang dicaj.');
    }
}
