<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Http\Requests\InvitationRequest;
use App\Models\Design;
use App\Models\Invitation;
use App\Models\PromotionBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->isManager()) {
            return to_route('manager.index');
        }

        return view('dashboard', [
            'promotion' => PromotionBanner::available()->latest('id')->first(),
            'invitations' => $request->user()->invitations()->latest()->paginate(9),
            'counts' => $request->user()->invitations()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function create(): View
    {
        return view('invitations.form', ['invitation' => new Invitation, 'themes' => config('catalog.themes')]);
    }

    public function store(InvitationRequest $request): RedirectResponse
    {
        $invitation = $request->user()->invitations()->create($request->validated());
        AuditContext::mark($request, 'invitation.created', $invitation);

        return to_route('invitations.options', $invitation)->with('status', 'Maklumat kad telah disimpan sebagai draf.');
    }

    public function edit(Request $request, int $invitation): View|RedirectResponse
    {
        $card = $request->user()->invitations()->findOrFail($invitation);

        return view('invitations.form', ['invitation' => $card, 'themes' => config('catalog.themes')]);
    }

    public function update(InvitationRequest $request, int $invitation): RedirectResponse
    {
        DB::transaction(function () use ($request, $invitation): void {
            $card = $request->user()->invitations()->lockForUpdate()->findOrFail($invitation);
            $before = AuditContext::snapshot($card);
            $wasCompleted = $card->status === 'demo_complete';
            $card->fill($request->validated());
            if ($wasCompleted) {
                $selectedDesign = Design::where('code', $card->design_code)->first();
                if (($selectedDesign['theme'] ?? null) !== $card->theme) {
                    $card->forceFill(['design_code' => null, 'design_name' => null]);
                }
                $card->save();
                AuditContext::mark($request, 'invitation.updated_after_payment', $card, $before);

                return;
            }
            $card->forceFill(['package_code' => null, 'package_name' => null, 'design_code' => null, 'design_name' => null, 'amount_cents' => null, 'status' => 'draft'])->save();
            AuditContext::mark($request, 'invitation.updated', $card, $before);
        });

        $card = $request->user()->invitations()->findOrFail($invitation);
        if ($card->status === 'demo_complete') {
            return to_route($card->design_code ? 'payments.show' : 'invitations.options', $card)
                ->with('status', $card->design_code ? 'Maklumat kad berjaya dikemas kini.' : 'Pilih design yang sepadan dengan tema baharu.');
        }

        return to_route('invitations.options', $invitation)->with('status', 'Maklumat dikemas kini. Sila sahkan semula pakej dan reka bentuk.');
    }

    public function options(Request $request, int $invitation): View|RedirectResponse
    {
        $card = $request->user()->invitations()->findOrFail($invitation);

        return view('invitations.options', [
            'invitation' => $card,
            'packages' => config('packages'),
            'designs' => Design::where('is_active', true)->where('theme', $card->theme)->orderBy('code')->get(),
        ]);
    }

    public function saveOptions(Request $request, int $invitation): RedirectResponse
    {
        DB::transaction(function () use ($request, $invitation): void {
            $card = $request->user()->invitations()->lockForUpdate()->findOrFail($invitation);
            $designs = Design::where('is_active', true)->where('theme', $card->theme)->get()->keyBy('code');
            if ($card->status === 'demo_complete') {
                $data = $request->validate([
                    'design_code' => ['required', Rule::in($designs->keys()->all())],
                ], [
                    'design_code.required' => 'Sila pilih satu reka bentuk.',
                    'design_code.in' => 'Reka bentuk tidak tersedia untuk tema ini.',
                ]);
                $before = AuditContext::snapshot($card);
                $design = $designs[$data['design_code']];
                $card->forceFill(['design_code' => $design['code'], 'design_name' => $design['name']])->save();
                AuditContext::mark($request, 'invitation.design_changed_after_payment', $card, $before);

                return;
            }
            $data = $request->validate([
                'package_code' => ['required', Rule::in(array_keys(config('packages')))],
                'design_code' => ['required', Rule::in($designs->keys()->all())],
            ], [
                'package_code.required' => 'Sila pilih satu pakej.',
                'package_code.in' => 'Pakej tidak sah.',
                'design_code.required' => 'Sila pilih satu reka bentuk.',
                'design_code.in' => 'Reka bentuk tidak tersedia untuk tema ini.',
            ]);
            if ($card->event_date->lt(today())) {
                throw ValidationException::withMessages(['event_date' => 'Tarikh majlis telah berlalu. Sila kemas kini maklumat kad.']);
            }
            $package = config('packages.'.$data['package_code']);
            $design = $designs[$data['design_code']];
            $before = AuditContext::snapshot($card);
            $card->forceFill([
                ...$data, 'package_name' => $package['name'], 'design_name' => $design['name'],
                'amount_cents' => $package['amount_cents'], 'status' => 'awaiting_payment',
            ])->save();
            AuditContext::mark($request, 'invitation.options_selected', $card, $before);
        });

        return to_route('payments.show', $invitation)->with('status', 'Pilihan design berjaya disimpan.');
    }
}
