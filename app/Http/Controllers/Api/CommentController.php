<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Requests\SaveCommentRequest;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store', 'update', 'destroy'],
        ]);
    }

    public function index()
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    public function store(SaveCommentRequest $request)
    {
        $comment = new Comment;
        $comment->body = $request->input('data.attributes.body');

        $comment->user_id = $request->getRelationshipId('author');

        $articleSlug = $request->getRelationshipId('article');
        $comment->article_id = Article::whereSlug($articleSlug)->firstOrFail()->id;

        $comment->save();

        return CommentResource::make($comment);
    }

    public function show(Comment $comment)
    {
        return CommentResource::make($comment);
    }

    public function update(SaveCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->body = $request->input('data.attributes.body');

        if ($request->hasRelationship('article')) {
            $articleSlug = $request->getRelationshipId('article');
            $comment->article_id = Article::whereSlug($articleSlug)->firstOrFail()->id;
        }
        if ($request->hasRelationship('author')) {
            $comment->user_id = $request->getRelationshipId('author');
        }

        $comment->save();

        return CommentResource::make($comment);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
