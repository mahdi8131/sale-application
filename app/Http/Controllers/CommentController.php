<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Notification;
use App\Events\NewCommentEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $validated['post_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content']
        ]);

        $comment->load('user');

        broadcast(new NewCommentEvent($comment))->toOthers();

        return response()->json(['comment' => $comment]);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted.']);
    }
}
