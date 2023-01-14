<?php

namespace App\Http\Controllers;

use App\Http\Resources\BinResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ImageResource;
use App\Models\Bin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\Facades\Image as InterventionImage;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class BinController extends Controller
{
    public function show(Request $request, Bin $bin)
    {
        if (!$bin->isPublished()) {
            abort(403);
        }

        views($bin)
            ->cooldown(now()->addHours(1))
            ->record();

        return Inertia::render('Bin/Show', [
            'bin' => new BinResource(
                $bin->load(['images', 'tags', 'comments'])
                    ->loadCount(['allComments'])
            ),
            'otherBins' => BinResource::collection(
                Bin::published()->latest()->limit(5)->get()
            )
        ]);
    }

    public function private(Request $request, Bin $bin)
    {
        if ($bin->isPublished()) {
            abort(403);
        }

        views($bin)
            ->cooldown(now()->addHours(1))
            ->record();

        return Inertia::render('Bin/Private', [
            'bin' => new BinResource(
                $bin->load(['images', 'tags', 'comments'])
                    ->loadCount(['allComments'])
            ),
            'otherBins' => BinResource::collection(
                Bin::published()->latest()->limit(5)->get()
            )
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'nullable', 'max:255'],
        ]);

        $bin = Bin::create($validated);

        if ($request->user()) {
            $request->user()->bins()->save($bin);
        }

        if ($request->wantsJson()) {
            return new BinResource($bin);
        }

        return redirect()->route('bin.edit', $bin);
    }

    public function edit(Request $request, Bin $bin)
    {
        return Inertia::render('Bin/Edit', [
            'bin' => new BinResource($bin->load(['images', 'tags', 'editHash'])),
        ]);
    }

    public function update(Request $request, Bin $bin)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'nullable', 'max:255'],
        ]);

        $bin->update($validated);

        if ($request->wantsJson()) {
            return new BinResource($bin);
        }

        return back();
    }

    public function destroy(Request $request, Bin $bin)
    {
        $bin->delete();

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'The bin has been deleted.',
            'duration' => 5000
        ]);

        return redirect()->route('home.index');
    }

    public function comments(Request $request, Bin $bin)
    {
        $request->validate([
            'comment' => ['required', 'max:140', 'min:10']
        ]);

        $comment = $bin->allComments()->create([
            'content' => $request->comment,
            'user_id' => Auth::id()
        ]);

        if ($request->wantsJson()) {
            return new CommentResource($comment);
        }

        return back();
    }

    public function favorite(Request $request, Bin $bin)
    {
        Auth::user()->toggleFavorite($bin);

        if ($request->wantsJson()) {
            return new BinResource($bin->fresh());
        }

        return back();
    }

    public function upvote(Request $request, Bin $bin)
    {
        if (Auth::user()->hasUpvoted($bin)) {
            Auth::user()->cancelVote($bin);
        } else {
            Auth::user()->upvote($bin);
        }

        if ($request->wantsJson()) {
            return new BinResource($bin->fresh());
        }

        return back();
    }

    public function downvote(Request $request, Bin $bin)
    {
        if (Auth::user()->hasDownvoted($bin)) {
            Auth::user()->cancelVote($bin);
        } else {
            Auth::user()->downvote($bin);
        }

        if ($request->wantsJson()) {
            return new BinResource($bin->fresh());
        }

        return back();
    }

    public function images(Request $request, Bin $bin)
    {
        if (!$bin->isPublished()) {
            $request->validate([
                'file' => ['sometimes', 'image', 'mimes:jpg,png,jpeg,gif,bmp,webp', 'max:5120'],
                'description' => ['sometimes', 'nullable', 'max:1000'],
            ]);

            $file = $request->file('file');
            $fileImage = InterventionImage::make($file);

            $image = $bin->images()->create([
                'description' => $request->description,
                'original_filename' => $fileImage->basename,
                'mime_type' => $fileImage->mime(),
                'size' => $fileImage->filesize(),
                'width' => $fileImage->width(),
                'height' => $fileImage->height(),
            ]);

            $file->storeAs($image->bin->directory_path, $image->filename);
        }

        if ($request->wantsJson()) {
            return new ImageResource($image);
        }

        return back();
    }

    public function tag(Request $request, Bin $bin)
    {
        if (!$bin->isPublished()) {
            $request->validate([
                'tag' => ['required'],
            ]);

            $bin->attachTags([$request->tag], 'bin');
        }

        if ($request->wantsJson()) {
            return new BinResource($bin->refresh()->load('tags'));
        }

        return back();
    }

    public function untag(Request $request, Bin $bin)
    {
        if (!$bin->isPublished()) {
            $request->validate([
                'tag' => ['required'],
            ]);

            $bin->detachTags([$request->tag], 'bin');
        }

        if ($request->wantsJson()) {
            return new BinResource($bin->refresh()->load('tags'));
        }

        return back();
    }

    public function publish(Request $request, Bin $bin)
    {
        $bin->update([
            'published_at' => now()
        ]);

        $bin->resetStats();

        if ($request->wantsJson()) {
            return new BinResource($bin);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'The bin has been published.',
            'duration' => 5000
        ]);

        return back();
    }

    public function unpublish(Request $request, Bin $bin)
    {
        $bin->update([
            'published_at' => null
        ]);

        $bin->resetStats();

        if ($request->wantsJson()) {
            return new BinResource($bin);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'The bin has been hidden.',
            'duration' => 5000
        ]);

        return back();
    }

    public function download(Request $request, Bin $bin)
    {
        $zipFilename = $bin->nanoid . '_' . Str::random(10) . '.zip';

        return response()->streamDownload(function () use ($bin, $zipFilename) {
            $options = new Archive();
            $options->setContentType('application/octet-stream');

            $zip = new ZipStream($zipFilename, $options);

            foreach ($bin->images as $image) {
                try {
                    $file = Storage::readStream($image->full_path);
                    $zip->addFileFromStream($image->filename, $file);
                } catch (Exception $e) {
                    Log::error("unable to read the file at storage path: $image->full_path and output to zip stream. Exception is " . $e->getMessage());
                }
            }

            $zip->finish();
        }, $zipFilename);
    }
}
