<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\Post;
use App\Models\User;

class HomeController extends Controller
{
    private $post;
    private $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $home_posts = $this->getHomePost();
        $suggested_users = $this->getSuggestedUsers();

        $all_posts = $this->post->latest()->get();
        return view('users.home')
                ->with('home_posts', $home_posts)
                ->with('suggested_users', $suggested_users);
    }

    public function getHomePost(){
        $all_posts = $this->post->latest()->get();
        $home_posts = []; // In case the the $home_posts at Line 35 is empty, it will not return NULL, but empty instead

        foreach ($all_posts as $post){
            if($post->user->isFollowed() || $post->user->id === Auth::user()->id){
                $home_posts[] = $post;
            }
        }

        return $home_posts;
    }

    public function getSuggestedUsers(){
        $all_users = $this->user->all()->except(Auth::user()->id);
        $suggested_users = [];

        foreach ($all_users as $user) {
            if (!$user->isFollowed()) {
                $suggested_users[] = $user;
            }
        }

        return array_slice($suggested_users, 0, 5);
        /*
        array_slice(x,y,z)
        x -- array
        y -- offset/starting index
        z -- length/how many
        */
    }
     # Search for a user name from the database
     public function search(Request $request)
     {
         $users = $this->user->where('name', 'like', '%'.$request->search.'%')->get();
         
         return view('users.search')
                 ->with('users', $users)
                 ->with('search', $request->search);
     }
}
