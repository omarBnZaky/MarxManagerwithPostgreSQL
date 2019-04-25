<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BookmarksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $bookmarks = Bookmark::where('name', 'LIKE', "%$keyword%")
                ->orWhere('url', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $bookmarks = Bookmark::where('user_id','=',Auth::user()->id )->paginate($perPage);
        }

        return view('bookmarks.index', compact('bookmarks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('bookmarks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'url' => 'required',
            'description' => 'required'
        ]);

        
        Bookmark::create([
            'name' => $request->input('name'),
            'url' =>  $request->input('url'),
            'description' =>  $request->input('description'),
            'user_id' => Auth::user()->id
        ]);

        return redirect('bookmarks')->with('flash_message', 'Bookmark added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $bookmark = Bookmark::findOrFail($id);

        return view('bookmarks.show', compact('bookmark'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $bookmark = Bookmark::findOrFail($id);

        return view('bookmarks.edit', compact('bookmark'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'url' => 'required',
            'description' => 'required'
        ]);
        
        $requestData = $request->all();
        
        $bookmark = Bookmark::findOrFail($id);
        $bookmark->update([
            'name' => $request->input('name'),
            'url' =>  $request->input('url'),
            'description' =>  $request->input('description'),
            'user_id' => Auth::user()->id
        ]);

        return redirect('bookmarks')->with('flash_message', 'Bookmark updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Bookmark::destroy($id);

        return redirect('bookmarks')->with('flash_message', 'Bookmark deleted!');
    }
}
