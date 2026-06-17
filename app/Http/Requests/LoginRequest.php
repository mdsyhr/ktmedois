<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\ExternalSupplier;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Username'      => ['required', 'string'],
            'Password_Hash' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Step 1 — Find user by username
        $user = User::where('Username', $this->input('Username'))->first();

        // Step 2 — Check if user exists and password matches
        if (! $user || ! Hash::check($this->input('Password_Hash'), $user->Password_Hash)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'Username' => trans('auth.failed'),
            ]);
        }

     // Step 3 — If vendor, check external supplier status
        if (strtolower($user->Role) === 'vendor') {
            
            // Try finding the external record by Email address (safest link)
            $externalSupplier = ExternalSupplier::where('SUPPLIER_EMAIL_ADD', $user->Email)->first();
            
            // If it can't find it by email, try matching by Username
            if (!$externalSupplier) {
                $externalSupplier = ExternalSupplier::where('SUPPLIERID', $user->Username)->first();
            }

            // Error Blocker 1: If the account doesn't exist at all in the external table
            if (!$externalSupplier) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'Username' => "System cannot find an external vendor record for Email: {$user->Email} or Username: {$user->Username}",
                ]);
            }

            // Get the status text and clean it up (lowercase and strip spaces)
            $currentStatus = trim(strtolower($externalSupplier->SUPPLIER_CTC_STATUS));

            // Error Blocker 2: If the status text inside the database is not exactly 'active'
            if ($currentStatus !== 'active') {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'Username' => "Access denied. Your external status is '{$externalSupplier->SUPPLIER_CTC_STATUS}'. It must be 'Active' to log in.",
                ]);
            }
        }

        // Step 4 — Log the user in
        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'Username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('Username')).'|'.$this->ip());
    }
}