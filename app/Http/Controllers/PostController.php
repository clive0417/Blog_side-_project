<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Post;
use App\Http\Requests\StoreBlogPost;
use App\Category;
use App\Http\Requests\StoreCategory;
use App\Tag;

class PostController extends Controller
{
    public function index() 
    {
        $posts = Post::all();
        $categories =Category::all();
        $tags =Tag::all();
        return view('posts.index',['posts'=>$posts,'categories'=>$categories,'tags'=>$tags]);

    }

    public function create() 
    {
        $post = new Post;
        $categories = Category::all();
        return view('posts.create',['post'=>$post,'categories'=>$categories]);

    } 

    // create method
    public function store(StoreBlogPost $request) // 用request 來承接create 所寫的資料 
    {

        $post = new Post;
        //->all 轉成array
        $post->fill($request->all());
        $post->user_id =Auth::id();
        $post->save();
        //redirect to index
 
        //TODO 
        // create /load tags
        $tags =explode(',',$request->tags);
        $this->addTagsToPost($tags,$post);

        //connent posts and tags
        return redirect('/posts/admin');


    } 

    private function addTagsToPost($tags,$post) 
    {
        foreach ($tags as $key=>$tag) {
            $model = Tag::firstOrCreate(['name'=> $tag]);
            $post->tags()->attach($model->id);
        }

    } 

    public function admin() 
    {
        $posts = Post::all();
        return view('posts.admin',['posts'=>$posts]);

    }
    public function indexWithCategory(Category $category) 
    {
        $posts= Post::where('category_id',$category->id)->get();
        $categories= Category::all();
        return view('posts.index',['posts'=>$posts,'categories'=>$categories]);


    }

    //tag split show 
    public function indexWithTag(Tag $tag) 
    {


        $posts = $tag->posts;

        return view('posts.index',['posts'=>$posts]);


    }

    //Post 代表POSt model
    public function show(Post $post) 
    {

        return view('posts.show',['post'=>$post]);

        

    }

    public function showByAdmin(Post $post) 
    {

        return view('posts.showByAdmin',['post'=>$post]);

    }


    public function edit(Post $post) 
    {     //['post'=>$post] 代表將資料傳入
        $categories = Category::all();
        return view('posts.edit',['post'=>$post,'categories'=>$categories]);


    }

    public function update(StoreBlogPost $request,Post $post) 
          // request 寫在第一個在
    {     //['post'=>$post] 代表將資料傳入

            

        $post->fill($request->all());//將request 的資料帶入，$post 變數中
        $post->save();//資料存到資料庫
        // remove old tags
        $post->tags()->detach();
        //foreach ($post->tags as $key =>$tag) {
            //$post->tags()->detach($tag->id);
        //}
        //tag update
        $tags =explode(',',$request->tags);
        $this->addTagsToPost($tags,$post);
        return redirect('/posts/admin'); 


    }

    public function destroy(Post $post) 
    {
        $post->delete();


    }



}
