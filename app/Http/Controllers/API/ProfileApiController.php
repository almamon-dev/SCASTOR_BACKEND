<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    use ApiResponse;

    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $userData = $user->toArray();
        $userData['avatar_url'] = Helper::generateURL($user->avatar);

        $apiResponse = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $userData['avatar_url'],
        ];

        return $this->sendResponse($apiResponse, 'Profile fetched successfully.');
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Email is not changeable
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
        ];

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Helper::deleteFile($user->avatar);
            }

            // Store new avatar
            $path = Helper::uploadFile('avatars', $request->file('avatar'));
            $data['avatar'] = $path;
        }

        $user->update($data);

        // Prepare response with avatar URL
        $userData = $user->refresh()->toArray();
        $userData['avatar_url'] = Helper::generateURL($user->avatar);

        return $this->sendResponse([], 'Profile updated successfully.');
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return $this->sendResponse([], 'Password updated successfully.');
    }
}
