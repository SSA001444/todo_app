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

                Image::make(storage_path('app/public/' . $imagePath))->resize(100, 50)->save();

                $imagePath = '/storage/'.$imagePath;
                $user->profile_photo = $imagePath;
                $user->save();

                return response()->json(['message' => 'Profile photo updated successfully']);
            }

        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return response()->json(['error' => 'No file uploaded'], 400);}
    }
}
