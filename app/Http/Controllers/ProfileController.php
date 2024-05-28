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
        $user->username = $validatedData['username'];

        // Handle email change request
        if ($validatedData['email'] !== $user->email) {
            $currentEmailToken = Str::random(60);

            // Create email change request
            $emailChange = EmailChange::create([
                'user_id' => $user->id,
                'new_email' => $validatedData['email'],
                'current_email_verification_token' => $currentEmailToken,
            ]);

            // Send verification email to the current email
            Mail::to($user->email)->send(new CurrentEmailChangeNotificationEmail($user, $currentEmailToken));

            return redirect()->route('profile.index')->with('success', 'Profile updated successfully. Please verify your current email address.');
        }

        // Update password if requested
        if ($request->input('change_password_hidden') === '1') {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
            } else {
                return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect'])->withInput();
            }
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully')->withInput();
    }

    public function verifyCurrentEmail($token)
    {
        $emailChange = EmailChange::where('current_email_verification_token', $token)->first();

        if (!$emailChange) {
            return redirect()->route('profile.index')->withErrors(['email' => 'Invalid or expired email verification token.']);
        }

        // Generate token for new email verification
        $newEmailToken = Str::random(60);
        $emailChange->new_email_verification_token = $newEmailToken;
        $emailChange->current_email_verification_token = null;
        $emailChange->save();

        // Send verification email to the new email
        Mail::to($emailChange->new_email)->send(new EmailChangeNotificationEmail($emailChange->user, $newEmailToken));

        return redirect()->route('profile.index')->with('success', 'Please verify your new email address.');
    }

    public function verifyNewEmail($token)
    {
        $emailChange = EmailChange::where('new_email_verification_token', $token)->first();

        if (!$emailChange) {
            return redirect()->route('profile.index')->withErrors(['email' => 'Invalid or expired email verification token.']);
        }

        // Update email and clear verification tokens
        $user = $emailChange->user;
        $user->email = $emailChange->new_email;
        $user->save();

        $emailChange->delete();

        return redirect()->route('profile.index')->with('success', 'Email address updated successfully.');
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

                return response()->json(['message' => 'Profile photo updated successfully']);
            }

        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            Log::error('Intervention Image cannot read the file: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
        }
    }
}
