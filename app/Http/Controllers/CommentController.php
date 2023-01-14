<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function destroy(Request $request, Comment $comment)
    {
        $comment->delete();

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return back();
    }

    public function replies(Request $request, Comment $comment)
    {
        $request->validate([
            'comment' => ['required', 'max:140', 'min:10']
        ]);

        $reply = $comment->commentable->allComments()->create([
            'content' => $request->comment,
            'user_id' => Auth::id(),
            'parent_id' => $comment->id,
        ]);

        if ($request->wantsJson()) {
            return new CommentResource($reply);
        }

        return back();
    }

    public function upvote(Request $request, Comment $comment)
    {
        if (Auth::user()->hasUpvoted($comment)) {
            Auth::user()->cancelVote($comment);
        } else {
            Auth::user()->upvote($comment);
        }

        if ($request->wantsJson()) {
            return new CommentResource($comment->fresh());
        }

        return back();
    }

    public function downvote(Request $request, Comment $comment)
    {
        if (Auth::user()->hasDownvoted($comment)) {
            Auth::user()->cancelVote($comment);
        } else {
            Auth::user()->downvote($comment);
        }

        if ($request->wantsJson()) {
            return new CommentResource($comment->fresh());
        }

        return back();
    }
}
