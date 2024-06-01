<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\CurrentEmailChangeNotificationEmail;
use App\Mail\EmailChangeNotificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailChange;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'current_password' => 'required_if:change_password_hidden,1',
            'new_password' => 'nullable|confirmed|min:8|required_if:change_password_hidden,1',
        ]);

        // Update user profile details
        $user->username = Crypt::encryptString($validatedData['username']);

        // Handle email change request
        if ($validatedData['email'] !== Crypt::decryptString($user->email)) {
            $currentEmailToken = Str::random(60);

            // Create email change request
            $emailChange = EmailChange::create([
                'user_id' => $user->id,
                'new_email' => Crypt::encryptString($validatedData['email']),
                'current_email_verification_token' => $currentEmailToken,
            ]);

            // Send verification email to the current email
            Mail::to(Crypt::decryptString($user->email))->send(new CurrentEmailChangeNotificationEmail($user, $currentEmailToken));

            return redirect()->route('profile.index')->with('success', __('messages.verify_current_email'));
        }

        // Update password if requested
        if ($request->input('change_password_hidden') === '1') {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            } else {
                return redirect()->back()->withErrors(['current_password' => __('messages.current_password_incorrect')])->withInput();
            }
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', __('messages.profile_updated'))->withInput();
    }

    public function verifyCurrentEmail($token)
    {
        $emailChange = EmailChange::where('current_email_verification_token', $token)->first();

        if (!$emailChange) {
            return redirect()->route('profile.index')->withErrors(['email' => __('messages.email_verification_token_invalid')]);
        }

        // Generate token for new email verification
        $newEmailToken = Str::random(60);
        $emailChange->new_email_verification_token = $newEmailToken;
        $emailChange->current_email_verification_token = null;
        $emailChange->save();

        // Send verification email to the new email
        Mail::to(Crypt::decryptString($emailChange->new_email))->send(new EmailChangeNotificationEmail($emailChange->user, $newEmailToken));

        return redirect()->route('profile.index')->with('success', __('messages.verify_new_email'));
    }

    public function verifyNewEmail($token)
    {
        $emailChange = EmailChange::where('new_email_verification_token', $token)->first();

        if (!$emailChange) {
            return redirect()->route('profile.index')->withErrors(['email' => __('messages.email_verification_token_invalid')]);
        }

        // Update email and clear verification tokens
        Crypt::encryptString($user = $emailChange->user);
        Crypt::encryptString($user->email = $emailChange->new_email);
        $user->save();

        $emailChange->delete();

        return redirect()->route('profile.index')->with('success', __('messages.email_updated'));
    }

    public function updatePhoto(Request $request)
    {
        try {
            $user = auth()->user();

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo != null) {
                    $filePath = public_path($user->profile_photo);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                        $user->profile_photo = null;
                        $user->save();
                    }
                }

                $image = $request->file('profile_photo');
                $imageName = 'profile_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'profile-photos/' . $imageName;

                // Move the image to public/profile-photos/
                $image->move(public_path('profile-photos'), $imageName);

                Image::make(public_path($imagePath))->resize(55, 55)->save();

                $user->profile_photo = '/' . $imagePath;
                Log::info('Saving profile photo', ['path' => $imagePath]);
                $user->save();

                return response()->json(['message' => __('messages.profile_photo_updated')]);
            }

        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            Log::error('Intervention Image cannot read the file: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
        }
    }
}
