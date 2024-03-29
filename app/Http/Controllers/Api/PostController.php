<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        $data = $posts->map(fn ($post) => $this->getPostDetail(post: $post));
        return $this->successResponse("Posts Retrived", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string|max:150|min:3|unique:posts,title",
            "content" => "required|string|min:20",
        ]);

        $postData = $request->collect(['title', 'content']);
        $postData->put("slug", str()->slug($request['title']));

        /**
         * @var User
         */
        $user = $request->user();
        $post = $user->posts()->create($postData->toArray());

        return $this->successResponse("Post Save Successfully", $this->getPostDetail($post->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        return $post
            ? $this->successResponse("Post Retrived", $this->getPostDetail(post: $post))
            : $this->failureResponse("Post Not Found");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "title" => "required|string|max:150|min:3|unique:posts,title,except,$id",
            "content" => "required|string|min:20",
        ]);

        $postData = $request->collect(['title', 'content']);
        $postData->put("slug", str()->slug($request['title']));

        $post = Post::find($id);
        return ($post && $post->update($postData->toArray()))
            ? $this->successResponse("Post Deleted")
            : $this->failureResponse("Post Not Found");;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        return ($post && $post->delete())
            ? $this->successResponse("Post Deleted")
            : $this->failureResponse("Post Not Found");;
    }

    private function getPostDetail($id = null, Post $post = null)
    {
        $id && $post = Post::find($id); // find the post if the id was provided

        return $post
            ? [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->title,
                'content' => $post->content,
                'user' => [
                    'name' => $post->user->name,
                    'username' => $post->user->username,
                    // 'space_name' => $post->space->name
                ]
            ]
            : false;
    }
}
