<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Walk;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\WalkRequest;

class WalkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // get request parameter
        $keyword = $request->keyword;

        // get walks info from keyword
        $query = Walk::query();
        if (!empty($keyword)) {
            $query->where('title', 'like', '%' . $keyword . '%');
            $query->orWhere('description', 'like', '%' . $keyword . '%');
            $query->orWhereHas('category', function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }
        $walks = $query->with('photos')->paginate(8);
        $walks->appends(compact('keyword'));

        // transfer view
        return view('walks.index', compact('walks', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // create new walk instance
        $walk = new Walk();

        // set initial latitude and longitude
        $walk->latitude = 39.91402764039571;
        $walk->longitude = 141.1007601246386;
        $zoom = 15;

        // get all categories
        $categories = Category::all()->sortBy('id');

        // transfer view
        return view('walks.create', compact('walk', 'zoom', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WalkRequest $request)
    {
        // create new walk instance
        $walk = new Walk();

        // set request form data
        $walk->fill($request->all());

        // set user id
        $walk->user_id = auth()->user()->id;

        // set files
        $files = $request->file;

        // begin transaction
        DB::beginTransaction();

        try {
            // save walk info
            $walk->save();

            // save multiple files
            $paths = [];
            foreach ($files as $file) {
                // get original name of file
                $name = $file->getClientOriginalName();
                // save files
                $path = Storage::putFile('walks', $file);
                if (!$path) {
                    throw new \Exception("Faild to save image...");
                }
                $paths[] = $path;
                // set photo info
                $photo = new Photo([
                    'walk_id' => $walk->id,
                    'org_name' => $name,
                    'name' => basename($path)
                ]);
                // save photo
                $photo->save();
            }

            // commit
            DB::commit();
        } catch (\Exception $e) {
            // file rollback(delete files)
            foreach ($paths as $path) {
                if (!empty($path)) {
                    Storage::delete($path);
                }
            }
            // db rollback
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        // redirect view
        return redirect()->route('walks.index')->with(['notice' => 'Complete Create New Walk.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function show(Walk $walk)
    {
        // set initial zoom
        $zoom = 15;

        // transfer view
        return view('walks.show', compact('walk', 'zoom'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function edit(Walk $walk)
    {
        // check authority to update
        $this->authorize('update', $walk);

        // set initial zoom
        $zoom = 15;

        // get all categories
        $categories = Category::all()->sortBy('id');

        // transfer view
        return view('walks.edit', compact('walk', 'zoom', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\WalkRequest  $request
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function update(WalkRequest $request, Walk $walk)
    {
        // check authority to update
        $this->authorize('update', $walk);

        // set request form data
        $walk->fill($request->all());

        // set files
        $files = $request->file;

        // begin transaction
        DB::beginTransaction();

        try {
            // save walk
            $walk->save();

            // save multiple files
            if (!empty($files)) {
                $paths = [];
                foreach ($files as $file) {
                    // get original name of file
                    $name = $file->getClientOriginalName();
                    // save files
                    $path = Storage::putFile('walks', $file);
                    if (!$path) {
                        throw new \Exception("Faild to save image...");
                    }
                    $paths[] = $path;
                    // set photo info
                    $photo = new Photo([
                        'walk_id' => $walk->id,
                        'org_name' => $name,
                        'name' => basename($path)
                    ]);
                    // save photo
                    $photo->save();
                }
            }

            // commit
            DB::commit();
        } catch (\Exception $e) {
            // file rollback(delete files)
            if (!empty($files)) {
                foreach ($paths as $path) {
                    if (!empty($path)) {
                        Storage::delete($path);
                    }
                }
            }
            // db rollback
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        // redirect view
        return redirect()
            ->route('walks.show', $walk)
            ->with(['notice' => 'Complete Edit New Walk.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Walk $walk)
    {

        // check authority to delete
        $this->authorize('delete', $walk);

        // get file path to delete
        $delete_file_paths = $walk->image_paths;

        // begin transavtion
        DB::beginTransaction();

        try {

            // delete walk
            $walk->delete();

            // delete files
            foreach ($delete_file_paths as $delete_file_path) {
                if (!Storage::delete($delete_file_path)) {
                    throw new \Exception('Faild to delete old image...');
                }
            }

            // db commit
            DB::commit();
        } catch (\Exception $e) {
            // db rollback
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        // redirect view
        return redirect()
            ->route('walks.index')
            ->with(['notice' => 'Complete Delete Walk.']);
    }
}
