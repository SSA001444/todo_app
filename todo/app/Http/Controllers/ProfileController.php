<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{

    public function updatePhoto(Request $request)
    {
        try{
            $user = auth()->user();

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo != null) {
                    $filePath = public_path($user->profile_photo);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                        $user->profile_photo = null;
                        $user->save();
                    }}

                $image = $request->file('profile_photo');
                $imageName = 'profile_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storePublicly('profile-photos', 'public');
                Image::make(storage_path('app/public/' . $imagePath))->resize(55, 55)->save();

                $imagePath = '/storage/'.$imagePath;
                $user->profile_photo = $imagePath;
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
