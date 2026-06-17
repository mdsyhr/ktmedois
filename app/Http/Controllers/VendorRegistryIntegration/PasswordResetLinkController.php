<?php

namespace App\Http\Controllers\VendorRegistryIntegration;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('usermanagement.auth.Change_PasswordView');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status == Password::RESET_LINK_SENT) {
        $user = \App\Models\User::where('Email', $request->email)->first();

        if ($user) {
            \App\Models\Notification::create([
                'User_ID'    => $user->User_ID,
                'Type'       => 'Password Reset',
                'Content'    => 'A password reset link was sent to ' . $user->Email,
                'Status'     => 'Sent',
                'Created_At' => now(),
            ]);
        }

        return back()->with('status', __($status));
    }

    return back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
