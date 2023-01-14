<?php

namespace App\Http\Controllers;

use App\Http\Resources\BinResource;
use App\Http\Resources\TagResource;
use App\Models\Bin;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    public function show(Request $request, $slug)
    {
        $tag = Tag::findBySlug($slug, 'bin');

        if (!$tag) {
            abort(404);
        }

        $bins = BinResource::collection(
            Bin::withAnyTags([$tag->name], 'bin')
                ->published()
                ->latest()
                ->paginate(6)
        );

        if ($request->wantsJson()) {
            return $bins;
        }

        return Inertia::render('Tag', [
            'tag' => new TagResource(
                $tag->loadCount(['bins' => function ($query) {
                    $query->published();
                }])
            ),
            'bins' => $bins,
        ]);
    }
}
