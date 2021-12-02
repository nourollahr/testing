<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class SingleController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()->latest()->paginate(15);

        return view('single', compact('post', 'comments'));
    }

    public function comment(Request $request, Post $post)
    {
        $user_id = auth()->user()->id;
        $post->comments()->create([
            'user_id' => $user_id,
            'text' => $request->input('text')
        ]);

        return [
            'created' => true
        ];
    }
}
