<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $before = AuditContext::snapshot($user);
        $data = $request->safe()->only(['name', 'email']);
        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }
        $user->update($data);
        AuditContext::mark($request, 'profile.updated', $user, $before);

        return to_route('profile.edit')->with('status', 'Profil berjaya dikemas kini.');
    }
}
