<?php

namespace App\Http\Controllers;

use App\Http\Resources\BinResource;
use App\Http\Resources\UserResource;
use App\Models\Bin;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class UserController extends Controller
{
    public function bins(Request $request, User $user)
    {
        $bins = BinResource::collection(
            Auth::check() && Auth::user() == $user ?
                $user->bins()->paginate(6) :
                $user->bins()->published()->paginate(6)
        );

        if ($request->wantsJson()) {
            return $bins;
        }

        return Inertia::render('User/Bins', [
            'user' => new UserResource($user),
            'points' => $user->bin_points,
            'bins' => $bins,
        ]);
    }

    public function favorites(Request $request, User $user)
    {
        $bins = BinResource::collection(
            $user->favorites(Bin::class)->paginate(6)
        );

        if ($request->wantsJson()) {
            return $bins;
        }

        return Inertia::render('User/Favorites', [
            'user' => new UserResource($user),
            'points' => $user->bin_points,
            'bins' => $bins,
        ]);
    }

    public function profileImage(Filesystem $filesystem, User $user)
    {
        $server = ServerFactory::create([
            'driver' => config('image.driver'),
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $filesystem->getDriver(),
            'cache' => Storage::disk('local')->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'profile',
        ]);

        return $server->getImageResponse(
            $user->profile_image_path,
            request()->all()
        );
    }
}
