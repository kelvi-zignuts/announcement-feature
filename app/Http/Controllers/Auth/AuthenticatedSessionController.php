<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // $remember = $request->has('remember');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // if (!$user->is_active) {
            //     Auth::logout();
            //     return redirect()->route('auth-login-basic')->withInput()->withErrors(['email' => 'Your account is not active. Please contact the administrator.']);
            // }

            // if (! $user->is_active && ! $user->isAdmin()) {
            //     Auth::logout();

            //     return redirect()->route('auth-login-basic')->withInput()->withErrors(['email' => 'Your account is not active. Please contact the administrator.']);
            // }

            // $token = $user->createToken('API Token')->plainTextToken;
            $token = $user->createToken('API Token')->plainTextToken;

            // if ($remember) {
            //     $rememberToken = Str::random(60);
            //     Cookie::queue('remember_email', $credentials['email'], 60 * 24 * 7);
            //     Cookie::queue('remember_password', $credentials['password'], 60 * 24 * 7);
            // } else {
            //     Cookie::queue(Cookie::forget('remember_email'));
            //     Cookie::queue(Cookie::forget('remember_password'));
            //     $rememberToken = null;
            // }

        //     if ($user->isAdmin()) {
        //         return redirect()->route('pages-home')->with('token', $token)->with('remember_token', $rememberToken);
        //     } else {
        //         return redirect()->route('user-dashboard')->with('token', $token)->with('remember_token', $rememberToken);
        //     }
        // } else {
        //     return redirect()->back()->withInput()->withErrors(['email' => 'Invalid credentials']);
        // }
        return response()->json([
            'status' => 'success',
            'message' => 'Announcement created successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }
    return response()->json([
        'status' => 'error',
        'message' => 'Announcement created not successfully'
    ], 401);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
