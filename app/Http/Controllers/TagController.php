<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // Show all tags in page
    public function TagPage() {
        $tags = Tag::all();
        return Inertia::render('TagPage', ['tags' => $tags]);
    }

    // Show tag for editing
    public function TagSavePage(Request $request) {
        $tag_id = $request->query('id');
        $tag = Tag::find($tag_id);
        return Inertia::render('TagSavePage', ['tag' => $tag]);
    }

    // Create a new tag
    public function CreateTag(Request $request) {
        Tag::create([
            'name' => $request->name
        ]);
        $data = ['message' => 'Tag created successfully', 'status' => true, 'error' => ''];
        return redirect('/TagPage')->with($data);
    }

    // Get all tags (API)
    public function TagList() {
        return Tag::all();
    }

    // Get tag by ID
    public function TagById(Request $request) {
        return Tag::find($request->id);
    }

    // Update existing tag
    public function TagUpdate(Request $request) {
        Tag::where('id', $request->input('id'))->update([
            'name' => $request->input('name')
        ]);
        $data = ['message' => 'Tag updated successfully', 'status' => true, 'error' => ''];
        return redirect('/TagPage')->with($data);
    }

    // Delete a tag
    public function TagDelete($id) {
        Tag::where('id', $id)->delete();
        $data = ['message' => 'Tag deleted successfully', 'status' => true, 'error' => ''];
        return redirect('/TagPage')->with($data);
    }
}
