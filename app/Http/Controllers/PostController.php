<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class PostController extends Controller
{
    public function fetchAndStorePosts(){
        // Fetch and cache the posts for 1 hour
        $posts = Cache::remember('storePost', 3600, function () {
            return Http::get('https://jsonplaceholder.typicode.com/posts')->json(); // Convert response to array
        });
    
        // Ensure the data is a collection
        $posts = collect($posts);
        //dd($posts);
        // Loop through each post and update or create it in the database
        foreach ($posts as $post) {

           // dd($post);
            Post::updateOrCreate(
                ['external_id' => $post['id']], // Ensure correct key names
                ['title' => $post['title'], 'body' => $post['body']]
            );
        }
    
        // Return paginated results
        return Post::paginate(5);
    }
}
