<?php

namespace App\Http\Controllers\Admin;;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\StarredPost;
use App\Models\ReadPost;
use Carbon\Carbon;
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Gate;
// use Yajra\DataTables\Facades\DataTables;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('feeds_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        if (\Auth::user()->roleaccess == '1')
            $posts = Post::whereNotNull('published_at')
                ->withCount(['readposts as view_counter' => function ($query) {
                    $query->where('read_flag', 1);
                }])
                ->withCount(['starredpost as stars_counter' => function ($query) {
                    $query->whereNotNull('updated_at');
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        else
            $posts = Post::all();

        // $user = Category::with('post')->get();
        // dd($posts);
        $categories = Category::all();
        $users = User::all();
        $author = User::with('post')->get();
        $category = Category::with('post')->get();
        $starred = StarredPost::all();

        $defaultimg = url('storage/img/post_img/default.jpg');

        $module_name = 'Post';
        $page_title = 'Artikel/Berita';
        $page_heading = 'Artikel/Berita';
        $heading_class = 'fal fa-rss';


        $delposts = Post::onlyTrashed()->get();

        return view('admin.posts.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'categories', 'users', 'author', 'posts', 'defaultimg', 'delposts'));
    }

    public function allblogs()
    {
        abort_if(Gate::denies('feeds_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (\Auth::user()->roleaccess != '1')
            $posts = Post::whereNotNull('published_at')
                ->withCount(['readposts as view_counter' => function ($query) {
                    $query->where('read_flag', 1);
                }])
                ->withCount(['starredpost as stars_counter' => function ($query) {
                    $query->whereNotNull('updated_at');
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        else
            $posts = Post::all();
        $categories = Category::all();
        $users = User::all();
        $author = User::with('post')->get();
        $category = Category::with('post')->get();
        $starred = StarredPost::all();

        $defaultimg = url('storage/img/post_img/default.jpg');

        $module_name = 'Post';
        $page_title = 'Artikel/Berita';
        $page_heading = 'Index Artikel/Berita';
        $heading_class = 'fal fa-rss';


        $delposts = Post::onlyTrashed()->get();

        return view('admin.posts.blogslist', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'categories', 'users', 'author', 'posts', 'defaultimg', 'delposts'));
    }

    public function show(Request $request, $id)
    {
        $module_name = 'Post';
        $page_title = 'Artikel/Berita';
        $page_heading = 'Artikel/Berita';
        $heading_class = 'fal fa-rss';

        $categories = Category::all();
        $post = Post::findOrFail($id);
        $starredPosts = StarredPost::with('posts')->where('user_id', $id)->get();
        $starredpost = StarredPost::all();

        $posts = Post::whereNotNull('published_at')
            ->withCount(['readposts as view_counter' => function ($query) {
                $query->where('read_flag', 1);
            }])
            ->withCount(['starredpost as stars_counter' => function ($query) {
                $query->whereNotNull('updated_at');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Trigger the read flag for the current user and post
        $readpost = Readpost::firstOrNew([
            'user_id' => auth()->user()->id,
            'post_id' => $post->id,
        ]);
        $readpost->read_flag = 1;
        $readpost->read_count = $readpost->read_count + 1;
        $readpost->save();

        return view('admin.posts.show', compact('post', 'readpost', 'starredpost', 'posts', 'starredPosts', 'categories', 'module_name', 'page_title', 'page_heading', 'heading_class'));
    }


    public function create()
    {
        // dd($request->all());
        $users = user::all();
        $categories = Category::all();

        $module_name = 'Post';
        $page_title = 'Artikel/Berita';
        $page_heading = 'Tambah Artikel/Berita';
        $heading_class = 'fal fa-rss';

        return view('admin.posts.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'users', 'categories'));
    }

    public function edit($id)
    {
        $post = Post::find($id);
        $categories = Category::all();
        $users  = User::with('post')->get();
        $defaultimg = url('storage/img/post_img/default.jpg');

        $module_name = 'Post';
        $page_title = 'Artikel/Berita';
        $page_heading = 'Perbarui Artikel/Berita';
        $heading_class = 'fal fa-rss';

        return view('admin.posts.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'categories', 'post', 'users', 'defaultimg'));
    }

    public function store(Request $request)
	{
		$request->validate([
			'title' => 'required',
			'summernoteInput' => 'required',
			'category' => 'required',
			'author' => 'required',
			'priority' => 'required',
			'is_active' => 'required',
			'visibility' => 'required',
			'exerpt' => 'required',
			'tags' => 'required',
			'img_cover' => 'nullable|image|mimes:jpg,png|max:2048', // Aturan validasi untuk gambar
		]);

		$detail = $request->summernoteInput;

		$post = new Post();
		$post->title = $request->input('title');
		$post->body = $detail;
		$post->category_id = $request->input('category');
		$post->user_id = $request->input('author');
		$post->priority = $request->input('priority');
		$post->is_active = $request->input('is_active');
		$post->visibility = $request->input('visibility');
		$post->exerpt = $request->input('exerpt');
		$post->tags = $request->input('tags');
		$author = User::find($request->input('author'));

		if ($request->hasFile('img_cover')) {
			// Validasi tipe berkas (JPG atau PNG)
			$request->validate([
				'img_cover' => 'image|mimes:jpg,png|max:2048',
			]);

			$image = $request->file('img_cover');
			$image_name = ($author->id) . '_' . $post->id . '_' . time() . '.' . $image->extension();
			Storage::disk('public')->putFileAs('img/post_img', $image, $image_name);
			$post->img_cover = $image_name;
		}

		$draft = $request->input('draft');
		if ($draft == 'on') {
			$post->published_at = Carbon::now();
		}

		$post->save();

		return redirect()->route('admin.posts.index')->with('success', 'Post created');
	}

	public function update(Request $request, Post $post)
	{
		$request->validate([
			'title' => 'required',
			'summernoteInput' => 'required',
			'exerpt' => 'required',
			'tags' => 'required',
			'category' => 'required',
			'author' => 'required',
			'priority' => 'required',
			'is_active' => 'required',
			'visibility' => 'required',
			'img_cover' => 'nullable|image|mimes:jpg,png|max:2048', // Aturan validasi untuk gambar
		]);

		$detail = $request->summernoteInput;

		$post->title = $request->input('title');
		$post->body = $detail;
		$post->exerpt = $request->input('exerpt');
		$post->tags = $request->input('tags');
		$post->category_id = $request->input('category');
		$post->user_id = $request->input('author');
		$post->priority = $request->input('priority');
		$post->is_active = $request->input('is_active');
		$post->visibility = $request->input('visibility');
		$author = User::find($request->input('author'));

		if ($request->hasFile('img_cover')) {
			// Validasi tipe berkas (JPG atau PNG)
			$request->validate([
				'img_cover' => 'image|mimes:jpg,png|max:2048',
			]);

			$image = $request->file('img_cover');
			$image_name = ($author->id) . '_' . $post->id . '_' . time() . '.' . $image->extension();
			Storage::disk('public')->putFileAs('img/post_img', $image, $image_name);
			$post->img_cover = $image_name;
		}

		$draft = $request->input('draft');
		if ($draft == 'on') {
			$post->published_at = Carbon::now();
		} else {
			$post->published_at = null;
		}

		$post->update();

		return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully');
	}

    public function star(Post $post)
    {
        $user = Auth::user();
        $user->starredPosts()->attach($post->id);
        return back()->withSuccess('Post starred successfully!');
    }

    public function unstar(Post $post)
    {
        $user = Auth::user();
        $user->starredPosts()->detach($post->id);
        return back()->withSuccess('Post unstarred successfully!');
    }

    public function delete(Request $request, Post $post)
    {
        $post->delete($request->all());
        $post->deleted_at = now();
        $post->is_deleted = '1';
        $post->save();
        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully');
    }


    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully');
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();
        return redirect()->route('admin.posts.index')->with('success', 'Post restored successfully');
    }
}
