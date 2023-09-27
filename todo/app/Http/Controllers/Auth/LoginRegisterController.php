<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;


class LoginRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'dashboard']);
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Attribute requirements and validation
        $request->validate([
            'username' => 'required|string|max:60|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $createUser = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // Create token for email verification
        $token = Str::random(40);

        UserVerify::create([
           'user_id' => $createUser->id,
           'token' => $token,
        ]);
        // Email sending site for verification
        Mail::send('email.verificationEmail', ['token' => $token], function($message) use($request) {
           $message->to($request->email);
           $message->subject('Email Verification Mail');
        });

        return redirect()->route('login')->with('success', 'We send email verification on your email');

    }

    public function verifyAccount($token)
    {
        $verifyUser = UserVerify::where('token', $token)->first();

        $message = 'Sorry your email cannot be identified.';
        // If email not verified - verification
        if (!is_null($verifyUser)) {
            $user = $verifyUser->user;

            if (!$user->is_email_verified) {
                $verifyUser->user->is_email_verified = 1;
                $verifyUser->user->save();
                $message = "Your e-mail is verified. You can now login.";
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }

        return redirect()->route('login')->with('success', $message);
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'identity' => 'required',
            'password' => 'required',
        ]);

        $identity = $request->input('identity');
        $password = $request->input('password');
        // Filter for credentials, email or username
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $identity, 'password' => $password];
        } else {
            $credentials = ['username' => $identity, 'password' => $password];
        }
        // If email is verified - authorization
        if (Auth::attempt($credentials)) {
            if (Auth::user()->is_email_verified) {
                $request->session()->regenerate();
                return redirect()->route('dashboard')
                    ->withSuccess('You have successfully logged in!');
            } else {
                Auth::logout();

                return redirect()->route('login')->withErrors(['identity' => 'Your email is not verified. Please verify your email.']);
            }
        } else {
            return redirect()->route('login')->withErrors([ 'identity' => 'Invalid credentials. Please try again.']);
        }
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }

        return redirect("login")->withSuccess('Opps! You do not have access');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }
}
