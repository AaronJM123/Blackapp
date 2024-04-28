<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;

class CommentArticleController extends Controller
{
    public function index(Comment $comment)
    {
        return ArticleResource::identifier($comment->article);
    }

    public function show(Comment $comment)
    {
        return ArticleResource::make($comment->article);
    }

    public function update(Comment $comment, Request $request)
    {
        $request->validate([
            'data.id' => ['exists:articles,slug'],
        ]);
        $articleSlug = $request->input('data.id');
        $article = Article::whereSlug($articleSlug)->firstOrFail();

        $comment->update(['article_id' => $article->id]);

        return ArticleResource::identifier($comment->article);
    }
}
