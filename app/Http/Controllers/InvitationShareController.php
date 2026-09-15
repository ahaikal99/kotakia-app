<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\View\View;

class InvitationShareController extends Controller
{
    public function show(string $token): View
    {
        $invitation = Invitation::query()
            ->where('public_token', $token)
            ->where('status', 'demo_complete')
            ->firstOrFail();

        return view('invitations.public', compact('invitation'));
    }
}
