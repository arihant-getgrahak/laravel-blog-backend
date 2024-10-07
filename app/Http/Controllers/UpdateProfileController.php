<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Storage;
use App\Models\User;

class UpdateProfileController extends Controller
{
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);

        $data = $request->only([
            "name",
            "email",
        ]);

        $isUpdate = $user->update(attributes: $data);

        if ($request->hasFile('image')) {
            $user->clearMediaCollection('user_photo');
            $user->addMediaFromRequest("image")->toMediaCollection('user_photo');
            $mediaItems = $user->getMedia("user_photo");
            $user["profile_image"] = $mediaItems[0]->original_url;
            $user->makeHidden("media");
        }
        if ($isUpdate) {
            return response()->json([
                "status" => true,
                "message" => "Profile updated successfully",
                "data" => $user
            ], 200);
        }
        return response()->json([
            "status" => false,
            "message" => "Unable to update profile",
        ], 500);

    }
}
