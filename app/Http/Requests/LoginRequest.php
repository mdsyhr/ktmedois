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
        // FIX 1: Make role comparison case-insensitive
        if (strtolower($user->Role) === 'vendor') {
            
            // FIX 2: Find the supplier by matching email instead of Username/ID
            $externalSupplier = ExternalSupplier::where('SUPPLIER_EMAIL_ADD', $user->Email)->first();

            if (! $externalSupplier) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'Username' => 'Vendor account details not found in the external supplier system.',
                ]);
            }

            // FIX 3: Convert the status to lowercase before comparing
            if (strtolower($externalSupplier->SUPPLIER_CTC_STATUS) !== 'active') {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'Username' => 'Your vendor account is inactive. Please contact KTMB administrator.',
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