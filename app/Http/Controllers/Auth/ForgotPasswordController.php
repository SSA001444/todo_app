<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Mail;
use Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;

class ForgotPasswordController extends Controller
{

    public function showForgetPasswordForm()
    {
        return view('auth.forgetPassword');
    }

    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);


        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $locale = App::getLocale();
        $view = 'email.forgetPassword.' . $locale;

        Mail::send($view, ['token' => $token], function($message) use($request) {
            $message->to($request->email);
            $message->subject(__('messages.reset_password'));
        });

        return back()->with('message', __('messages.password_reset_link_sent'));
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.forgetPasswordLink', ['token' => $token]);
    }

    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        $resetPasswordEntries = DB::table('password_resets')
                                  ->where('token', $request->token)
                                  ->get();

        $emailMatch = false;

        foreach ($resetPasswordEntries as $entry) {
            if (Crypt::decryptString($entry->email) === $request->email) {
                $emailMatch = true;
                break;
            }
        }

        if (!$emailMatch) {
            return back()->withInput()->with('error', __('messages.invalid_token'));
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput()->with('error', __('messages.user_not_found'));
        }
        // Setting new password
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('message', __('messages.password_changed'));
    }
}
