<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Intervention\Image\Facades\Image as InterventionImage;
use Symfony\Component\Mime\MimeTypes;

class AccountController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('users')->ignore($user)],
            'email' => ['sometimes', 'required', 'string', 'email', Rule::unique('users')->ignore($user)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,png,jpeg,gif,bmp,webp', 'max:1024']
        ]);

        $user->update($request->only(['name', 'username', 'email']));

        if ($request->file('profile_image')) {
            $file = $request->file('profile_image');

            $fileImage = InterventionImage::make($file)
                ->fit(300, 300, function ($constraint) {
                    $constraint->upsize();
                })->stream();

            $image = $fileImage->__toString();

            $ext = MimeTypes::getDefault()->getExtensions($file->getMimeType())[0];

            $filename = $user->nanoid . '_' . Str::random(5) . '.' . $ext;

            Storage::put(
                'profile_images/' . $filename,
                $image,
            );

            $user->update([
                'profile_image' => $filename
            ]);
        }

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Your account settings has been updated.',
            'duration' => 5000
        ]);

        return back();
    }

    public function deleteProfileImage(Request $request)
    {
        $user = Auth::user();

        Storage::delete('profile_images/' . $user->profile_image);

        $user->update([
            'profile_image' => null
        ]);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Your profile image has been removed.',
            'duration' => 5000
        ]);

        return back();
    }

    public function settings(Request $request)
    {
        return Inertia::render('Account/Settings', [
            'user' => new UserResource(Auth::user()),
        ]);
    }

    public function password(Request $request)
    {
        return Inertia::render('Account/Password', [
            'user' => new UserResource(Auth::user()),
        ]);
    }
}
