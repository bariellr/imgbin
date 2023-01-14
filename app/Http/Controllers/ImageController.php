<?php

namespace App\Http\Controllers;

use App\Http\Resources\BinResource;
use App\Http\Resources\ImageResource;
use App\Models\Bin;
use App\Models\Image;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class ImageController extends Controller
{
    public function show(Request $request, Image $image)
    {
        views($image)
            ->cooldown(now()->addHours(1))
            ->record();

        return Inertia::render('Image/Show', [
            'image' => new ImageResource($image),
            'otherBins' => BinResource::collection(
                Bin::published()->latest()->limit(5)->get()
            )
        ]);
    }

    public function update(Request $request, Image $image)
    {
        $validated = $request->validate([
            'description' => ['sometimes', 'nullable'],
        ]);

        $image->update($validated);

        if ($request->wantsJson()) {
            return new ImageResource($image);
        }

        return back();
    }

    public function destroy(Request $request, Image $image)
    {
        if (!$image->bin->isPublished()) {
            $image->delete();
        }

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'The image has been deleted.',
            'duration' => 5000
        ]);

        return back();
    }

    public function favorite(Request $request, Image $image)
    {
        Auth::user()->toggleFavorite($image);

        if ($request->wantsJson()) {
            return new ImageResource($image->fresh());
        }

        return back();
    }

    public function url(Filesystem $filesystem, Image $image)
    {
        $server = ServerFactory::create([
            'driver' => config('image.driver'),
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $filesystem->getDriver(),
            'cache' => Storage::disk('local')->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'img',
        ]);

        return $server->getImageResponse(
            $image->full_path,
            request()->all()
        );
    }

    public function download(Filesystem $filesystem, Image $image)
    {
        $headers = [
            'Content-Type' => $image->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $image->filename . '"',
        ];

        return Response::make(
            Storage::get($image->full_path),
            200,
            $headers
        );
    }
}
