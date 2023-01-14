<?php

namespace App\Http\Controllers;

use App\Http\Resources\BinResource;
use App\Http\Resources\TagResource;
use App\Models\Bin;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $tags = TagResource::collection(
            Tag::withType('bin')
                ->whereHas('bins')
                ->withCount(['bins' => function ($query) {
                    $query->published();
                }])
                ->get()
        );

        $bins = BinResource::collection(
            Bin::published()
                ->when($request->q, function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->q . '%');
                })
                ->latest()
                ->paginate(6)
        );

        if ($request->wantsJson()) {
            return $bins;
        }

        return Inertia::render('Home', [
            'bins' => $bins,
            'tags' => $tags,
            'q' => $request->q
        ]);
    }
}
