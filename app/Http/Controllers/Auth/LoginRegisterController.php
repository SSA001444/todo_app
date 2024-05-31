<?php

namespace App\Http\Controllers\Auth;

use App\Models\ChatContact;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;
use Illuminate\Support\Facades\Crypt;

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
            'username' => Crypt::encryptString($request->username),
            'email' => Crypt::encryptString($request->email),
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

        return redirect()->route('login')->with('success', __('messages.email_verification_sent'));

    }

    public function verifyAccount($token)
    {
        $verifyUser = UserVerify::where('token', $token)->first();

        $message = __('messages.email_verification_error');
        // If email not verified - verification
        if (!is_null($verifyUser)) {
            $user = $verifyUser->user;

            if (!$user->is_email_verified) {
                $verifyUser->user->is_email_verified = 1;
                $verifyUser->user->save();
                $message = __('messages.email_verification_success');;
            } else {
                $message = __('messages.email_verification_already');
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

        $users = User::all();

        foreach ($users as $user) {
            $decryptedEmail = Crypt::decryptString($user->email);
            $decryptedUsername = Crypt::decryptString($user->username);

            if ($decryptedEmail === $identity || $decryptedUsername === $identity) {
                if (Hash::check($password, $user->password)) {
                    Auth::login($user);
                    if (Auth::user()->is_email_verified) {
                        $request->session()->regenerate();
                        return redirect()->route('dashboard')
                            ->withSuccess(__('messages.login_success'));
                    } else {
                        Auth::logout();
                        return back()->withErrors(['identity' => __('messages.email_not_verified')]);
                    }
                } else {
                    return back()->withErrors(['identity' => __('messages.invalid_credentials')]);
                }
            }
        }

        return back()->withErrors(['identity' =>  __('messages.invalid_credentials')]);
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }

        return redirect("login")->withSuccess(__('messages.access_denied'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withSuccess(__('messages.logout_success'));
    }
}
