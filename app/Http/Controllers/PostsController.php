<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;
use DB; // bring db libary

class PostsController extends Controller
{
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$posts = post::all();  // (Elequent)
        //$posts = DB::select('SELECT * FROM posts'); // Use DB SQL 
        // return Post::where('title', 'Post two')->get();  Get only post two (Elequent)
        //$posts = Post::OrderBy('title','desc')->get();   //Order by desc  ( Elequent)
        // $posts = Post::OrderBy('title','desc')->take(1)->get();   //Order by desc and get 1 element 

        $posts = Post::OrderBy('Created_at','desc')->paginate(10);   //Paginate  ( Elequent)
        return view('posts.index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'body'=>'required',
            'cover_image' => ' image|nullable|max:1999'
        ]);

        //Hande file upload
        if($request->hasFile('cover_image')){
            //Get file name with the extention
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get just file name
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            //Uploadimage
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }else{
            $fileNameToStore = 'noimage.jpg';
        }

        //Creat post
        $post = new post;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();

        return redirect('/posts')->with('success','Post Created');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post =  Post::find($id);
        return view('posts.show')->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post =  Post::find($id);

        // Check for correct user
        if(auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error','Unautorized page');  
        }
        return view('posts.edit')->with('post',$post);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title'=>'required',
            'body'=>'required'
        ]);

          //Hande file upload
          if($request->hasFile('cover_image')){
            //Get file name with the extention
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get just file name
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //Get just extension
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            //Uploadimage
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }

        //Find post
        $post = post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        if($request->hasFile('cover_image')){
            $post->cover_image=$fileNameToStore;
        }
        $post->save();

        return redirect('/posts')->with('success','Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post= post::find($id);
         // Check for correct user
         if(auth()->user()->id !== $post->user_id){
            return redirect('/posts')->with('error','Unautorized page');  
        }
        if($post->cover_image != 'noimage.jpg'){
            // Delete the image
            Storage::delete('public/cover_image/' . $post->cover_image);
        }

        $post->delete();
        return redirect('/posts')->with('success','Post Removed');

    }
}
