<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        Comment::create([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
            'body'    => $request->input('body'),
        ]);

        return back();
    }
}
