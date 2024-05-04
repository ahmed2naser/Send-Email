<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Notifications\emailCode;
use Twilio\Rest\Client;

class LoginRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }


    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // insert code in data
        $user = User::where('email', $this->input('email'))->first();
        $user->generateCode();

        // send email
        $user->notify(new emailCode());

        // send mobile
        // $message = "Your Code" . $user->code;
        // $account_sid = getenv("TWILIO_SID");
        // $account_token = getenv("TWILIO_TOKEN");
        // $twilio_number = getenv("TWILIO_FROM");
        // $client = new Client($account_sid, $account_token);
        // $client->Messages->create('+2000010101010', [
        //     'form' => $twilio_number,
        //     'bode' => $message
        // ]);

        RateLimiter::clear($this->throttleKey());
    }


    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }


    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
