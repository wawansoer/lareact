<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleAuthService;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleSignInController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): \Illuminate\Http\RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google authentication callback.
     *
     * This method processes the callback from Google authentication,
     * attempts to authenticate the user, and redirects them appropriately.
     * If successful, redirects to the intended dashboard. If there's an
     * invalid state or other error, redirects back to login with an error message.
     *
     * @param  GoogleAuthService  $googleAuthService  The service handling Google authentication
     * @return \Illuminate\Http\RedirectResponse Redirect response to dashboard or login
     *
     * @throws InvalidStateException When the authentication state is invalid
     * @throws \Throwable For any other authentication errors
     */
    public function callback(
        GoogleAuthService $googleAuthService
    ): \Illuminate\Http\RedirectResponse {
        try {
            $googleAuthService->handleCallback();

            return redirect()->intended('/dashboard');
        } catch (InvalidStateException $e) {
            Log::error($e);

            return redirect('/login')->withErrors(['email' => 'Invalid state. Please try again.']);
        } catch (\Throwable $th) {
            Log::error($th);

            return redirect('/login')->withErrors(['email' => 'Unable to login using Google. Please try again.']);
        }
    }
}
