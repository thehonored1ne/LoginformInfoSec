<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showForgotPassword()
    {
        return view('pages.forgot-password');
    }

    /**
     * Handle the password reset link request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $email = strtolower(trim($request->email));
        $key = 'password-reset-limit:' . $email . '|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many reset attempts. Please try again in {$seconds} seconds.",
            ])->withInput();
        }

        $user = UserModel::where('email', $email)->first();

        if (!$user) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }


        // Generate a random token
        $token = Str::random(64);

        // Increment rate limit hit to prevent spamming
        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send the email
        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);
        
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\ResetPasswordEmail($resetLink));
            Log::info("Password reset link sent to {$email}: {$resetLink}");
        } catch (\Exception $e) {
            Log::error("Failed to send reset email: " . $e->getMessage());
            // Fallback for current testing: still log the link so user can use it
            Log::info("FALLBACK: Password reset link for {$email}: {$resetLink}");
        }


        return back()->with('status', 'We have emailed your password reset link!');
    }


    /**
     * Show the form to reset the password.
     */
    public function showResetPassword(Request $request, $token)
    {
        return view('pages.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Handle the password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $email = strtolower(trim($request->email));

        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Invalid token or email.']);
        }

        // Check if token is expired (e.g., 60 minutes)
        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'This password reset token has expired.']);
        }

        $user = UserModel::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }


        // Update password using the custom hashing mechanism
        $salt = bin2hex(random_bytes(16));
        $user->password = hash('sha256', $request->password . $salt);
        $user->salt = $salt;
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $email)->delete();


        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
}
