<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function CreatePost(Request $request)
    {
        $user_id = $request->header('id');

        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'visibility' => 'required|in:public,private',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility,
            'user_id' => $user_id
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'uploads/posts/' . $fileName;
            $image->move(public_path('uploads/posts'), $fileName);
            $data['image'] = $filePath;
        }

        $post = Post::create($data);
        $post->tags()->sync($request->tags ?? []);

        return redirect('/PostPage')->with([
            'message' => 'Post created successfully',
            'status' => true,
            'error' => ''
        ]);
    }

    public function PostPage(Request $request)
    {
        $user_id = $request->header('id');

        $query = Post::with('user', 'tags')->where('user_id', $user_id);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%")
                    ->orWhereHas('tags', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('user', fn($q) => $q->where('username', 'like', "%$search%"));
            });
        }

        $posts = $query->latest()->get();

        return Inertia::render('PostPage', [
            'posts' => $posts,
            'search' => $request->search
        ]);
    }

    public function PostSavePage(Request $request)
    {
        $user_id = $request->header('id');
        $post_id = $request->query('id');

        $post = Post::with('tags')->where('id', $post_id)
            ->where('user_id', $user_id)->first();

        $tags = Tag::all();

        return Inertia::render('PostSavePage', [
            'post' => $post,
            'tags' => $tags
        ]);
    }

    public function PostList(Request $request)
    {
        $user_id = $request->header('id');
        return Post::where('user_id', $user_id)->with('tags')->latest()->get();
    }

    public function PostById(Request $request)
    {
        $user_id = $request->header('id');
        return Post::where('id', $request->id)->where('user_id', $user_id)
            ->with('tags')->first();
    }

    public function PostDetail(Request $request, $id)
    {
        $user_id = $request->header('id');

        $post = Post::with(['user', 'tags', 'comments.user', 'comments.replies.user'])
            ->where('id', $id)
            ->where(function ($q) use ($user_id) {
                $q->where('visibility', 'public')
                    ->orWhere('user_id', $user_id);
            })
            ->firstOrFail();

        return Inertia::render('PostDetail', ['post' => $post]);
    }


    public function PostUpdate(Request $request)
    {
        $user_id = $request->header('id');

        $request->validate([
            'id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'visibility' => 'required|in:public,private',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        $post = Post::where('user_id', $user_id)->findOrFail($request->id);

        $post->title = $request->title;
        $post->content = $request->content;
        $post->visibility = $request->visibility;

        if ($request->hasFile('image')) {
            if ($post->image && file_exists(public_path($post->image))) {
                unlink(public_path($post->image));
            }

            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = 'uploads/posts/' . $fileName;
            $image->move(public_path('uploads/posts'), $fileName);
            $post->image = $filePath;
        }

        $post->save();
        $post->tags()->sync($request->tags ?? []);

        return redirect('/PostPage')->with([
            'message' => 'Post updated successfully',
            'status' => true,
            'error' => ''
        ]);
    }

    public function PostDelete(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);

            if ($post->image && file_exists(public_path($post->image))) {
                unlink(public_path($post->image));
            }

            $post->tags()->detach();
            $post->delete();

            return redirect()->back()->with([
                'message' => 'Post deleted successfully',
                'status' => true,
                'error' => ''
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with([
                'message' => 'Something went wrong!',
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
